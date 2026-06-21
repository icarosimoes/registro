import { cookies } from "next/headers";

const API_URL = process.env.API_URL ?? "http://localhost:8000/api/v1";

export async function getPlatformToken(): Promise<string | null> {
  const store = await cookies();
  return store.get("platform_token")?.value ?? null;
}

export async function platformFetch<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const token = await getPlatformToken();
  if (!token) throw new Error("unauthorized");
  const res = await fetch(`${API_URL}${path}`, {
    ...init,
    cache: "no-store",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
      ...(init.headers ?? {}),
    },
  });
  if (res.status === 401) throw new Error("unauthorized");
  if (!res.ok) throw new Error(`Platform API ${res.status}`);
  if (res.status === 204) return undefined as T;
  return res.json();
}
