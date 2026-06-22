from typing import Any

import httpx
import structlog

logger = structlog.get_logger()

TIMEOUT = 30.0


class AsaasError(Exception):
    def __init__(self, status: int, body: Any):
        self.status = status
        self.body = body
        super().__init__(f"Asaas API error {status}")


class AsaasClient:
    def __init__(self, api_key: str, base_url: str = "https://sandbox.asaas.com/api/v3"):
        self.base_url = base_url.rstrip("/")
        self.headers = {
            "access_token": api_key,
            "Content-Type": "application/json",
        }

    async def _request(
        self,
        method: str,
        path: str,
        *,
        json: dict | None = None,
        params: dict | None = None,
    ) -> dict:
        url = f"{self.base_url}{path}"
        async with httpx.AsyncClient(timeout=TIMEOUT) as client:
            resp = await client.request(
                method,
                url,
                headers=self.headers,
                json=json,
                params=params,
            )
        if resp.status_code >= 400:
            logger.error("asaas_error", method=method, path=path, status=resp.status_code)
            raise AsaasError(resp.status_code, resp.json() if resp.content else {})
        return resp.json()

    # ----- Customers -----

    async def create_customer(
        self,
        *,
        name: str,
        email: str,
        cpf_cnpj: str,
        external_reference: str,
    ) -> dict:
        return await self._request(
            "POST",
            "/customers",
            json={
                "name": name,
                "email": email,
                "cpfCnpj": cpf_cnpj,
                "externalReference": external_reference,
            },
        )

    async def get_customer(self, customer_id: str) -> dict:
        return await self._request("GET", f"/customers/{customer_id}")

    # ----- Subscriptions -----

    async def create_subscription(
        self,
        *,
        customer_id: str,
        billing_type: str = "BOLETO",
        value: float,
        cycle: str = "MONTHLY",
        description: str,
        external_reference: str,
    ) -> dict:
        return await self._request(
            "POST",
            "/subscriptions",
            json={
                "customer": customer_id,
                "billingType": billing_type,
                "value": value,
                "cycle": cycle,
                "description": description,
                "externalReference": external_reference,
            },
        )

    async def get_subscription(self, subscription_id: str) -> dict:
        return await self._request("GET", f"/subscriptions/{subscription_id}")

    async def cancel_subscription(self, subscription_id: str) -> dict:
        return await self._request("DELETE", f"/subscriptions/{subscription_id}")

    # ----- Payments -----

    async def list_payments(
        self,
        *,
        subscription_id: str | None = None,
        customer_id: str | None = None,
    ) -> list[dict]:
        params: dict[str, str] = {}
        if subscription_id:
            params["subscription"] = subscription_id
        if customer_id:
            params["customer"] = customer_id
        data = await self._request("GET", "/payments", params=params)
        return data.get("data", [])

    async def get_payment(self, payment_id: str) -> dict:
        return await self._request("GET", f"/payments/{payment_id}")
