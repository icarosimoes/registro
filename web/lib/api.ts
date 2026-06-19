import { cookies } from "next/headers";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

export type TenantUser = {
  id: number;
  name: string;
  email: string;
  company_id: number;
  role_name: string | null;
  permissions: string[];
};

export async function currentTenantUser(): Promise<TenantUser> {
  const token = (await cookies()).get("tenant_token")?.value;
  if (!token) throw new Error("unauthorized");

  const response = await fetch(`${apiUrl}/auth/me`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (!response.ok) throw new Error(response.status === 401 ? "unauthorized" : "api_error");
  return response.json() as Promise<TenantUser>;
}
