import { describe, it, expect, vi, beforeEach } from "vitest";

// Mock next/headers
const mockCookieJar = {
  get: vi.fn(),
  set: vi.fn(),
  delete: vi.fn(),
};
vi.mock("next/headers", () => ({
  cookies: vi.fn(() => mockCookieJar),
}));

// Mock next/navigation
const mockRedirect = vi.fn();
vi.mock("next/navigation", () => ({
  redirect: mockRedirect,
}));

// Mock lib/auth
vi.mock("@/lib/auth", () => ({
  setTokenCookies: vi.fn(),
  tryRefreshToken: vi.fn(),
}));

// Mock global fetch
const mockFetch = vi.fn();
global.fetch = mockFetch;

import { loginAction, logoutAction } from "@/app/actions";
import { setTokenCookies } from "@/lib/auth";

describe("loginAction", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("returns ok:true and sets cookies on successful login", async () => {
    mockFetch.mockResolvedValue({
      ok: true,
      status: 200,
      json: () =>
        Promise.resolve({
          access_token: "at_123",
          refresh_token: "rt_456",
          expires_in: 1800,
        }),
    });

    const result = await loginAction("user@test.com", "pass123");

    expect(result).toEqual({ ok: true });
    expect(mockFetch).toHaveBeenCalledWith(
      expect.stringContaining("/auth/login"),
      expect.objectContaining({
        method: "POST",
        body: JSON.stringify({ email: "user@test.com", password: "pass123" }),
      }),
    );
    expect(setTokenCookies).toHaveBeenCalledWith({
      access_token: "at_123",
      refresh_token: "rt_456",
      expires_in: 1800,
    });
  });

  it("returns error on invalid credentials", async () => {
    mockFetch.mockResolvedValue({
      ok: false,
      status: 401,
      json: () => Promise.resolve({ detail: "Invalid credentials" }),
    });

    const result = await loginAction("bad@test.com", "wrong");

    expect(result).toEqual({ ok: false, error: "E-mail ou senha inválidos." });
    expect(setTokenCookies).not.toHaveBeenCalled();
  });

  it("returns multi_tenant with tenants list on 422", async () => {
    mockFetch.mockResolvedValue({
      ok: false,
      status: 422,
      json: () =>
        Promise.resolve({
          detail: {
            code: "multi_tenant",
            tenants: [
              { id: 1, name: "Hotel A" },
              { id: 2, name: "Hotel B" },
            ],
          },
        }),
    });

    const result = await loginAction("multi@test.com", "pass");

    expect(result).toEqual({
      ok: false,
      multi_tenant: true,
      tenants: [
        { id: 1, name: "Hotel A" },
        { id: 2, name: "Hotel B" },
      ],
    });
  });

  it("sends company_id when provided", async () => {
    mockFetch.mockResolvedValue({
      ok: true,
      status: 200,
      json: () =>
        Promise.resolve({
          access_token: "at",
          refresh_token: "rt",
          expires_in: 1800,
        }),
    });

    await loginAction("user@test.com", "pass", 42);

    expect(mockFetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        body: JSON.stringify({ email: "user@test.com", password: "pass", company_id: 42 }),
      }),
    );
  });
});

describe("logoutAction", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("deletes cookies and redirects to /login", async () => {
    await logoutAction().catch(() => {
      // redirect throws in Next.js, ignore
    });

    expect(mockCookieJar.delete).toHaveBeenCalledWith("tenant_token");
    expect(mockCookieJar.delete).toHaveBeenCalledWith("tenant_refresh_token");
    expect(mockRedirect).toHaveBeenCalledWith("/login");
  });
});
