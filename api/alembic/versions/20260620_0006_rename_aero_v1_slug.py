"""Renomeia slug aero-v1 para aero-hotel se existir, sem duplicar."""

from collections.abc import Sequence

from alembic import op
from sqlalchemy import text

revision: str = "20260620_0006"
down_revision: str | None = "20260620_0005"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    conn = op.get_bind()
    has_v1 = conn.execute(text("SELECT id FROM companies WHERE slug = 'aero-v1'")).scalar()
    if has_v1 is None:
        return
    has_hotel = conn.execute(text("SELECT id FROM companies WHERE slug = 'aero-hotel'")).scalar()
    if has_hotel is not None:
        conn.execute(text(
            "UPDATE users SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE occurrences SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE sectors SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE locations SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE functions SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE procedures SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text(
            "UPDATE fiscal_requests SET company_id = :target WHERE company_id = :source"
        ).bindparams(target=has_hotel, source=has_v1))
        conn.execute(text("DELETE FROM companies WHERE id = :source").bindparams(source=has_v1))
    else:
        conn.execute(text(
            "UPDATE companies SET slug = 'aero-hotel', name = 'Aero Hotel' WHERE id = :id"
        ).bindparams(id=has_v1))


def downgrade() -> None:
    pass
