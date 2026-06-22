import { getValidToken, tryRefreshToken } from "./auth";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

export type TenantUser = {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  company_id: number;
  role_name: string | null;
  permissions: string[];
};

export async function currentTenantUser(): Promise<TenantUser> {
  const token = await getValidToken();
  const response = await fetch(`${apiUrl}/auth/me`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (response.status === 401) {
    const newToken = await tryRefreshToken();
    if (!newToken) throw new Error("unauthorized");
    const retry = await fetch(`${apiUrl}/auth/me`, {
      headers: { Authorization: `Bearer ${newToken}` },
      cache: "no-store",
    });
    if (!retry.ok) throw new Error("unauthorized");
    return retry.json() as Promise<TenantUser>;
  }
  if (!response.ok) throw new Error("api_error");
  return response.json() as Promise<TenantUser>;
}

export async function tenantFetch<T>(path: string): Promise<T> {
  const token = await getValidToken();
  const response = await fetch(`${apiUrl}${path}`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (response.status === 401) {
    const newToken = await tryRefreshToken();
    if (!newToken) throw new Error("unauthorized");
    const retry = await fetch(`${apiUrl}${path}`, {
      headers: { Authorization: `Bearer ${newToken}` },
      cache: "no-store",
    });
    if (!retry.ok) throw new Error("api_error");
    return retry.json() as Promise<T>;
  }
  if (!response.ok) throw new Error("api_error");
  return response.json() as Promise<T>;
}
