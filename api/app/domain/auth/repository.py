from dataclasses import dataclass

from sqlalchemy import text
from sqlalchemy.ext.asyncio import AsyncSession


@dataclass(frozen=True)
class LegacyUser:
    id: int
    name: str
    email: str
    password_hash: str
    company_id: int
    role_id: int | None
    role_name: str | None
    permissions: list[str]


async def find_active_user_by_email(session: AsyncSession, email: str) -> LegacyUser | None:
    result = await session.execute(text("""
        SELECT u.id, u.name, u.email, u.password, u.company_id, u.role_id,
               r.name AS role_name
        FROM users AS u
        LEFT JOIN roles AS r ON r.id = u.role_id
        WHERE LOWER(u.email) = LOWER(:email)
          AND u.deleted_at IS NULL AND COALESCE(u.status, 1) = 1
        LIMIT 1
    """), {"email": email})
    row = result.mappings().first()
    if row is None or row["company_id"] is None:
        return None

    permissions: list[str] = []
    if row["role_id"] is not None:
        permission_rows = await session.execute(text("""
            SELECT DISTINCT CONCAT(COALESCE(m.name, '*'), ':', a.controller, ':', a.action)
            FROM role_acl AS ra
            JOIN acls AS a ON a.id = ra.acl_id
            LEFT JOIN modules AS m ON m.id = a.module_id
            WHERE ra.role_id = :role_id
            ORDER BY 1
        """), {"role_id": row["role_id"]})
        permissions = list(permission_rows.scalars().all())

    return LegacyUser(
        id=row["id"], name=row["name"], email=row["email"],
        password_hash=row["password"], company_id=row["company_id"],
        role_id=row["role_id"], role_name=row["role_name"], permissions=permissions,
    )


async def find_active_user_by_id(
    session: AsyncSession, user_id: int, company_id: int,
) -> LegacyUser | None:
    result = await session.execute(
        text(
            "SELECT email FROM users WHERE id = :id AND company_id = :company_id "
            "AND deleted_at IS NULL LIMIT 1"
        ),
        {"id": user_id, "company_id": company_id},
    )
    email = result.scalar_one_or_none()
    return await find_active_user_by_email(session, email) if email else None
