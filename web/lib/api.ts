import { z } from "zod";

import { getValidToken, tryRefreshToken } from "./auth";
import { safeParse, TenantUserSchema } from "./schemas";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

export type TenantUser = z.infer<typeof TenantUserSchema>;

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
    return safeParse(TenantUserSchema, await retry.json());
  }
  if (!response.ok) throw new Error("api_error");
  return safeParse(TenantUserSchema, await response.json());
}

export async function tenantFetch<T>(path: string, schema?: z.ZodType<T>): Promise<T> {
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
    const data = await retry.json();
    return schema ? safeParse(schema, data) : (data as T);
  }
  if (!response.ok) throw new Error("api_error");
  const data = await response.json();
  return schema ? safeParse(schema, data) : (data as T);
}
