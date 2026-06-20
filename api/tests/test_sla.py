"""Testes do módulo de SLA: cálculo de dias úteis, timezone e pausa."""

from datetime import UTC, datetime, timedelta
from types import SimpleNamespace

from app.core.sla import (
    calculate_business_deadline,
    compute_sla_status,
    pause_sla,
    resume_sla,
)


class TestComputeSlaStatus:
    def test_none_deadline_returns_none(self):
        assert compute_sla_status(None, "Em andamento") is None

    def test_completed_status(self):
        deadline = datetime.now(UTC) - timedelta(hours=1)
        assert compute_sla_status(deadline, "Concluído") == "completed"

    def test_completed_status_cancelado(self):
        deadline = datetime.now(UTC) + timedelta(hours=10)
        assert compute_sla_status(deadline, "Cancelado") == "completed"

    def test_overdue(self):
        deadline = datetime.now(UTC) - timedelta(hours=1)
        assert compute_sla_status(deadline, "Em andamento") == "overdue"

    def test_warning_within_4_hours(self):
        deadline = datetime.now(UTC) + timedelta(hours=2)
        assert compute_sla_status(deadline, "Em andamento") == "warning"

    def test_on_time(self):
        deadline = datetime.now(UTC) + timedelta(hours=10)
        assert compute_sla_status(deadline, "Em andamento") == "on_time"

    def test_paused_status(self):
        deadline = datetime.now(UTC) + timedelta(hours=10)
        paused_at = datetime.now(UTC)
        assert compute_sla_status(deadline, "Em espera", paused_at=paused_at) == "paused"

    def test_paused_seconds_extends_deadline(self):
        deadline = datetime.now(UTC) - timedelta(seconds=100)
        result = compute_sla_status(deadline, "Em andamento", paused_seconds=200)
        assert result == "warning"

    def test_naive_deadline_treated_as_utc(self):
        deadline = (datetime.now(UTC) + timedelta(hours=10)).replace(tzinfo=None)
        assert compute_sla_status(deadline, "Em andamento") == "on_time"


class TestCalculateBusinessDeadline:
    def test_within_same_business_day(self):
        monday_10am = datetime(2026, 6, 22, 13, 0, 0, tzinfo=UTC)  # 10am BRT
        deadline = calculate_business_deadline(
            monday_10am, sla_hours=4, timezone="America/Sao_Paulo"
        )
        expected_local_hour = 14  # 10 + 4 = 14h local
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.hour == expected_local_hour
        assert dl_local.weekday() == 0  # still Monday

    def test_spans_to_next_day(self):
        monday_10am = datetime(2026, 6, 22, 13, 0, 0, tzinfo=UTC)  # 10am BRT
        deadline = calculate_business_deadline(
            monday_10am, sla_hours=10, timezone="America/Sao_Paulo"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 1  # Tuesday
        assert dl_local.hour == 10  # 8h left Mon + 2h Tue = 10h

    def test_skips_weekend(self):
        friday_10am = datetime(2026, 6, 26, 13, 0, 0, tzinfo=UTC)  # 10am BRT Friday
        deadline = calculate_business_deadline(
            friday_10am, sla_hours=10, timezone="America/Sao_Paulo"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 0  # Monday

    def test_starts_on_weekend_moves_to_monday(self):
        saturday = datetime(2026, 6, 27, 10, 0, 0, tzinfo=UTC)
        deadline = calculate_business_deadline(
            saturday, sla_hours=2, timezone="America/Sao_Paulo"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 0  # Monday
        assert dl_local.hour == 10  # 8h start + 2h

    def test_starts_after_work_hours_moves_to_next_morning(self):
        monday_22utc = datetime(2026, 6, 22, 22, 0, 0, tzinfo=UTC)  # 19h BRT, past work end
        deadline = calculate_business_deadline(
            monday_22utc, sla_hours=2, timezone="America/Sao_Paulo"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 1  # Tuesday
        assert dl_local.hour == 10

    def test_holidays_skipped(self):
        monday_10am = datetime(2026, 6, 22, 13, 0, 0, tzinfo=UTC)
        holidays = {"2026-06-22"}
        deadline = calculate_business_deadline(
            monday_10am, sla_hours=2, timezone="America/Sao_Paulo", holidays=holidays
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 1  # Tuesday

    def test_different_timezone(self):
        monday_14utc = datetime(2026, 6, 22, 14, 0, 0, tzinfo=UTC)  # 10am EDT
        deadline = calculate_business_deadline(
            monday_14utc, sla_hours=4, timezone="America/New_York"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/New_York"))
        assert dl_local.hour == 14
        assert dl_local.weekday() == 0

    def test_returns_naive_utc(self):
        dt = datetime(2026, 6, 22, 13, 0, 0, tzinfo=UTC)
        deadline = calculate_business_deadline(dt, sla_hours=2)
        assert deadline.tzinfo is None

    def test_24h_sla_spans_multiple_days(self):
        monday_10am = datetime(2026, 6, 22, 13, 0, 0, tzinfo=UTC)
        deadline = calculate_business_deadline(
            monday_10am, sla_hours=24, timezone="America/Sao_Paulo"
        )
        from zoneinfo import ZoneInfo
        dl_local = deadline.replace(tzinfo=UTC).astimezone(ZoneInfo("America/Sao_Paulo"))
        assert dl_local.weekday() == 2  # Wednesday (8+10+6 = 24)
        assert dl_local.hour == 14  # 24 - 8 (Mon) - 10 (Tue) = 6h into Wed → 8+6=14


class TestPauseResume:
    def _make_record(self):
        return SimpleNamespace(sla_paused_at=None, sla_paused_seconds=0)

    def test_pause_sets_paused_at(self):
        record = self._make_record()
        pause_sla(record)
        assert record.sla_paused_at is not None

    def test_pause_idempotent(self):
        record = self._make_record()
        pause_sla(record)
        first = record.sla_paused_at
        pause_sla(record)
        assert record.sla_paused_at == first

    def test_resume_accumulates_seconds(self):
        record = self._make_record()
        record.sla_paused_at = (datetime.now(UTC) - timedelta(minutes=30)).replace(tzinfo=None)
        resume_sla(record)
        assert record.sla_paused_at is None
        assert record.sla_paused_seconds >= 1790

    def test_resume_without_pause_is_noop(self):
        record = self._make_record()
        resume_sla(record)
        assert record.sla_paused_seconds == 0
        assert record.sla_paused_at is None
