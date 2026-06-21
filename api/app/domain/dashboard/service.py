from datetime import datetime

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import FiscalRequest, Occurrence, Sector, User

async def get_metrics(
    session: AsyncSession,
    company_id: int,
    user_id: int,
) -> dict:
    base = [Occurrence.company_id == company_id, Occurrence.deleted_at.is_(None)]

    open_occurrences = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(*base, Occurrence.status.in_([1, 3]))
        )
        or 0
    )

    my_occurrences = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(
                *base, Occurrence.status.in_([1, 3]), Occurrence.owner_user_id == user_id
            )
        )
        or 0
    )

    open_fiscal = (
        await session.scalar(
            select(func.count(FiscalRequest.id)).where(
                FiscalRequest.company_id == company_id,
                FiscalRequest.status != "Concluído",
            )
        )
        or 0
    )

    now = datetime.now()
    month_start = now.replace(day=1, hour=0, minute=0, second=0, microsecond=0)
    completed_month = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(
                *base, Occurrence.status == 2, Occurrence.updated_at >= month_start
            )
        )
        or 0
    )

    active_users = (
        await session.scalar(
            select(func.count(User.id)).where(
                User.company_id == company_id,
                User.active.is_(True),
                User.deleted_at.is_(None),
            )
        )
        or 0
    )

    active_sectors = (
        await session.scalar(
            select(func.count(Sector.id)).where(
                Sector.company_id == company_id, Sector.deleted_at.is_(None)
            )
        )
        or 0
    )

    recent = await _recent_all(session, company_id)

    return {
        "open_occurrences": open_occurrences,
        "my_occurrences": my_occurrences,
        "open_fiscal": open_fiscal,
        "completed_month": completed_month,
        "active_users": active_users,
        "active_sectors": active_sectors,
        "recent": recent,
    }


async def _recent_all(session: AsyncSession, company_id: int) -> list[dict]:
    from sqlalchemy import text as raw

    sql = raw("""
        SELECT * FROM (
            (SELECT o.id, o.title, 'Ocorrências' AS module,
                    COALESCE(s.name, 'Sem setor') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    CASE o.status WHEN 1 THEN 'Em andamento'
                                  WHEN 2 THEN 'Concluído'
                                  WHEN 3 THEN 'Aguardando'
                                  ELSE 'Em andamento' END AS status,
                    o.updated_at
             FROM occurrences o
             LEFT JOIN sectors s ON s.id = o.sector_id
             LEFT JOIN users u ON u.id = o.owner_user_id
             WHERE o.company_id = :cid AND o.deleted_at IS NULL
             ORDER BY o.updated_at DESC LIMIT 5)
            UNION ALL
            (SELECT m.id, m.title, 'Reuniões' AS module,
                    COALESCE(m.location, 'Geral') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    COALESCE(m.status, 'Agendada') AS status,
                    m.updated_at
             FROM meetings m
             LEFT JOIN users u ON u.id = m.owner_user_id
             WHERE m.company_id = :cid AND m.deleted_at IS NULL
             ORDER BY m.updated_at DESC LIMIT 5)
            UNION ALL
            (SELECT sr.id, sr.title, 'Relatórios de turno' AS module,
                    COALESCE(sr.shift_type, 'Geral') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    COALESCE(sr.status, 'Em andamento') AS status,
                    sr.updated_at
             FROM shift_reports sr
             LEFT JOIN users u ON u.id = sr.owner_user_id
             WHERE sr.company_id = :cid AND sr.deleted_at IS NULL
             ORDER BY sr.updated_at DESC LIMIT 5)
            UNION ALL
            (SELECT fr.id, COALESCE(fr.title, fr.request_type) AS title,
                    'Solicitações Fiscais' AS module,
                    COALESCE(fr.request_type, 'Fiscal') AS area,
                    COALESCE(fr.requester, 'Não atribuído') AS owner,
                    COALESCE(fr.status, 'Em andamento') AS status,
                    fr.updated_at
             FROM fiscal_requests fr
             WHERE fr.company_id = :cid
             ORDER BY fr.updated_at DESC LIMIT 5)
            UNION ALL
            (SELECT mr.id, mr.title, 'Inspeções' AS module,
                    COALESCE(mr.category, 'Geral') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    COALESCE(mr.status, 'Em andamento') AS status,
                    mr.updated_at
             FROM module_records mr
             LEFT JOIN users u ON u.id = mr.owner_user_id
             WHERE mr.company_id = :cid AND mr.deleted_at IS NULL
               AND mr.module = 'inspecoes'
             ORDER BY mr.updated_at DESC LIMIT 3)
            UNION ALL
            (SELECT mr.id, mr.title, 'Manutenção' AS module,
                    COALESCE(mr.category, 'Geral') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    COALESCE(mr.status, 'Em andamento') AS status,
                    mr.updated_at
             FROM maintenance_records mr
             LEFT JOIN users u ON u.id = mr.owner_user_id
             WHERE mr.company_id = :cid AND mr.deleted_at IS NULL
             ORDER BY mr.updated_at DESC LIMIT 3)
            UNION ALL
            (SELECT mr.id, mr.title, 'Auditoria Noturna' AS module,
                    COALESCE(mr.category, 'Geral') AS area,
                    COALESCE(u.name, 'Não atribuído') AS owner,
                    COALESCE(mr.status, 'Em andamento') AS status,
                    mr.updated_at
             FROM module_records mr
             LEFT JOIN users u ON u.id = mr.owner_user_id
             WHERE mr.company_id = :cid AND mr.deleted_at IS NULL
               AND mr.module = 'manutencao'
             ORDER BY mr.updated_at DESC LIMIT 3)
        ) combined
        ORDER BY updated_at DESC
    """)

    rows = (await session.execute(sql, {"cid": company_id})).all()

    result = []
    for row in rows:
        result.append({
            "id": row.id,
            "title": row.title or "Sem título",
            "module": row.module or "",
            "area": row.area or "Geral",
            "owner": row.owner or "Não atribuído",
            "status": row.status or "Em andamento",
            "updated_at": row.updated_at,
        })

    return result
