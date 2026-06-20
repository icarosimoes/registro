import { cookies } from "next/headers";

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

async function tryRefreshToken(): Promise<string | null> {
  const jar = await cookies();
  const refreshToken = jar.get("tenant_refresh_token")?.value;
  if (!refreshToken) return null;

  const response = await fetch(`${apiUrl}/auth/refresh`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ refresh_token: refreshToken }),
    cache: "no-store",
  });
  if (!response.ok) return null;

  const data = (await response.json()) as {
    access_token: string;
    refresh_token: string;
    expires_in: number;
  };
  jar.set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  jar.set("tenant_refresh_token", data.refresh_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 60 * 60 * 24 * 7,
  });
  return data.access_token;
}

async function getValidToken(): Promise<string> {
  const jar = await cookies();
  const token = jar.get("tenant_token")?.value;
  if (token) return token;
  const refreshed = await tryRefreshToken();
  if (refreshed) return refreshed;
  throw new Error("unauthorized");
}

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
