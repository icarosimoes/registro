"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

export async function loginAction(formData: FormData) {
  const response = await fetch(`${apiUrl}/auth/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      email: formData.get("email"),
      password: formData.get("password"),
      company_slug: formData.get("company_slug"),
    }),
    cache: "no-store",
  });

  if (!response.ok) redirect("/login?error=1");

  const data = (await response.json()) as { access_token: string; expires_in: number };
  (await cookies()).set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  redirect("/dashboard");
}

export async function logoutAction() {
  (await cookies()).delete("tenant_token");
  redirect("/login");
}
