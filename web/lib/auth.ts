import { cookies } from "next/headers";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

const COOKIE_OPTIONS = {
  httpOnly: true,
  sameSite: "lax" as const,
  secure: process.env.COOKIE_SECURE === "true" || process.env.NODE_ENV === "production",
  path: "/",
};

interface TokenData {
  access_token: string;
  refresh_token: string;
  expires_in: number;
}

export async function setTokenCookies(data: TokenData): Promise<void> {
  const jar = await cookies();
  jar.set("tenant_token", data.access_token, {
    ...COOKIE_OPTIONS,
    maxAge: data.expires_in,
  });
  jar.set("tenant_refresh_token", data.refresh_token, {
    ...COOKIE_OPTIONS,
    maxAge: 60 * 60 * 24 * 7,
  });
}

export async function tryRefreshToken(): Promise<string | null> {
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

  const data = (await response.json()) as TokenData;
  await setTokenCookies(data);
  return data.access_token;
}

export async function getValidToken(): Promise<string> {
  const jar = await cookies();
  const token = jar.get("tenant_token")?.value;
  if (token) return token;
  const refreshed = await tryRefreshToken();
  if (refreshed) return refreshed;
  throw new Error("unauthorized");
}
