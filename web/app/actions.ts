"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

interface LoginResult {
  ok: boolean;
  error?: string;
  multi_tenant?: boolean;
  tenants?: { id: number; name: string }[];
}

export async function loginAction(
  email: string,
  password: string,
  companyId?: number,
): Promise<LoginResult> {
  const body: Record<string, unknown> = { email, password };
  if (companyId) body.company_id = companyId;

  const response = await fetch(`${apiUrl}/auth/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
    cache: "no-store",
  });

  if (response.status === 422) {
    const data = await response.json();
    if (data.detail?.code === "multi_tenant") {
      return {
        ok: false,
        multi_tenant: true,
        tenants: data.detail.tenants,
      };
    }
  }

  if (!response.ok) {
    return { ok: false, error: "E-mail ou senha inválidos." };
  }

  const data = (await response.json()) as { access_token: string; expires_in: number };
  (await cookies()).set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  return { ok: true };
}

export async function logoutAction() {
  (await cookies()).delete("tenant_token");
  redirect("/login");
}
