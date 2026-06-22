from datetime import UTC, datetime, timedelta
from zoneinfo import ZoneInfo

DEFAULT_WORK_START = 8
DEFAULT_WORK_END = 18
DEFAULT_SLA_HOURS = 24


def _is_business_day(dt: datetime, holidays: set[str] | None = None) -> bool:
    if dt.weekday() >= 5:
        return False
    return not (holidays and dt.strftime("%Y-%m-%d") in holidays)


def calculate_business_deadline(
    start_utc: datetime,
    sla_hours: int = DEFAULT_SLA_HOURS,
    timezone: str = "America/Sao_Paulo",
    holidays: set[str] | None = None,
    work_start: int = DEFAULT_WORK_START,
    work_end: int = DEFAULT_WORK_END,
) -> datetime:
    """Avança sla_hours em horário útil (seg-sex, work_start-work_end) na timezone do tenant.

    Retorna datetime UTC naive (para armazenar no banco).
    """
    tz = ZoneInfo(timezone)
    current = start_utc.replace(tzinfo=UTC).astimezone(tz)
    remaining = sla_hours * 3600.0

    current = _advance_to_next_work_moment(current, work_start, work_end, holidays)

    while remaining > 0:
        end_of_day = current.replace(hour=work_end, minute=0, second=0, microsecond=0)
        available = (end_of_day - current).total_seconds()

        if remaining <= available:
            result = current + timedelta(seconds=remaining)
            return result.astimezone(UTC).replace(tzinfo=None)

        remaining -= available
        current = _next_business_morning(current, work_start, work_end, holidays)

    return current.astimezone(UTC).replace(tzinfo=None)


def _advance_to_next_work_moment(
    dt: datetime,
    work_start: int,
    work_end: int,
    holidays: set[str] | None,
) -> datetime:
    if not _is_business_day(dt, holidays) or dt.hour >= work_end:
        return _next_business_morning(dt, work_start, work_end, holidays)
    if dt.hour < work_start:
        return dt.replace(hour=work_start, minute=0, second=0, microsecond=0)
    return dt


def _next_business_morning(
    dt: datetime,
    work_start: int,
    work_end: int,
    holidays: set[str] | None,
) -> datetime:
    next_day = (dt + timedelta(days=1)).replace(hour=work_start, minute=0, second=0, microsecond=0)
    while not _is_business_day(next_day, holidays):
        next_day += timedelta(days=1)
    return next_day


def compute_sla_status(
    deadline: datetime | None,
    status: str,
    paused_at: datetime | None = None,
    paused_seconds: int = 0,
) -> str | None:
    if deadline is None:
        return None

    terminal = {"concluído", "concluido", "cancelado"}
    if status.casefold() in terminal:
        return "completed"

    if paused_at is not None:
        return "paused"

    now = datetime.now(UTC)
    dl = deadline.replace(tzinfo=UTC) if deadline.tzinfo is None else deadline
    effective_dl = dl + timedelta(seconds=paused_seconds)
    remaining = (effective_dl - now).total_seconds()

    if remaining <= 0:
        return "overdue"
    if remaining <= 3600 * 4:
        return "warning"
    return "on_time"


def pause_sla(record) -> None:
    """Marca o início da pausa no record (in-place)."""
    if record.sla_paused_at is None:
        record.sla_paused_at = datetime.now(UTC).replace(tzinfo=None)


def resume_sla(record) -> None:
    """Encerra a pausa e acumula os segundos pausados."""
    if record.sla_paused_at is not None:
        paused_at = record.sla_paused_at.replace(tzinfo=UTC)
        elapsed = (datetime.now(UTC) - paused_at).total_seconds()
        record.sla_paused_seconds += int(max(elapsed, 0))
        record.sla_paused_at = None
