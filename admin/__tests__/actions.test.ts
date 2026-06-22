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

// Mock global fetch
const mockFetch = vi.fn();
global.fetch = mockFetch;

import { loginAction, logoutAction } from "@/lib/actions";

describe("admin loginAction", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("sets cookies and redirects on success", async () => {
    mockFetch.mockResolvedValue({
      ok: true,
      status: 200,
      json: () =>
        Promise.resolve({
          access_token: "platform_at",
          refresh_token: "platform_rt",
          expires_in: 1800,
        }),
    });

    const formData = new FormData();
    formData.set("email", "admin@registro.com");
    formData.set("password", "admin123");

    await loginAction(formData).catch(() => {
      // redirect throws in test env
    });

    expect(mockFetch).toHaveBeenCalledWith(
      expect.stringContaining("/platform/auth/login"),
      expect.objectContaining({
        method: "POST",
        body: JSON.stringify({ email: "admin@registro.com", password: "admin123" }),
      }),
    );
    expect(mockCookieJar.set).toHaveBeenCalledWith(
      "platform_token",
      "platform_at",
      expect.objectContaining({ httpOnly: true }),
    );
    expect(mockCookieJar.set).toHaveBeenCalledWith(
      "platform_refresh_token",
      "platform_rt",
      expect.objectContaining({ httpOnly: true }),
    );
    expect(mockRedirect).toHaveBeenCalledWith("/");
  });

  it("redirects to /login?error=1 on failure", async () => {
    mockFetch.mockResolvedValue({
      ok: false,
      status: 401,
      json: () => Promise.resolve({ detail: "Invalid" }),
    });

    const formData = new FormData();
    formData.set("email", "bad@test.com");
    formData.set("password", "wrong");

    await loginAction(formData).catch(() => {
      // redirect throws
    });

    expect(mockRedirect).toHaveBeenCalledWith("/login?error=1");
    expect(mockCookieJar.set).not.toHaveBeenCalled();
  });
});

describe("admin logoutAction", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("clears cookies and redirects to /login", async () => {
    await logoutAction().catch(() => {
      // redirect throws
    });

    expect(mockCookieJar.delete).toHaveBeenCalledWith("platform_token");
    expect(mockCookieJar.delete).toHaveBeenCalledWith("platform_refresh_token");
    expect(mockRedirect).toHaveBeenCalledWith("/login");
  });
});
