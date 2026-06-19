import { cookies } from "next/headers";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

export async function platformFetch<T>(path: string): Promise<T> {
  const token = (await cookies()).get("platform_token")?.value;
  if (!token) throw new Error("unauthorized");
  const response = await fetch(`${apiUrl}${path}`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (!response.ok) throw new Error(response.status === 401 ? "unauthorized" : "api_error");
  return response.json() as Promise<T>;
}
