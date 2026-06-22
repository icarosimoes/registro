from app.core.pagination import decode_cursor, encode_cursor


class TestEncodeCursor:
    def test_roundtrip(self):
        cursor = encode_cursor(42)
        assert decode_cursor(cursor) == 42

    def test_roundtrip_large_id(self):
        cursor = encode_cursor(999999)
        assert decode_cursor(cursor) == 999999


class TestDecodeCursor:
    def test_invalid_base64(self):
        assert decode_cursor("!!!invalid!!!") is None

    def test_wrong_prefix(self):
        import base64

        bad = base64.urlsafe_b64encode(b"x:123").decode()
        assert decode_cursor(bad) is None

    def test_non_numeric_id(self):
        import base64

        bad = base64.urlsafe_b64encode(b"c:abc").decode()
        assert decode_cursor(bad) is None
