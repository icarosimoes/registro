import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen, fireEvent, waitFor } from "@testing-library/react";

// Mock the loginAction before importing the component
const mockLoginAction = vi.fn();
vi.mock("@/app/actions", () => ({
  loginAction: (...args: unknown[]) => mockLoginAction(...args),
}));

// Mock useRouter with a trackable push
const mockPush = vi.fn();
vi.mock("next/navigation", () => ({
  useRouter: () => ({
    push: mockPush,
    replace: vi.fn(),
    back: vi.fn(),
    refresh: vi.fn(),
  }),
}));

import LoginPage from "@/app/login/page";

describe("LoginPage", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("renders email and password fields", () => {
    render(<LoginPage />);
    expect(screen.getByLabelText(/e-mail/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/senha/i)).toBeInTheDocument();
  });

  it("renders the submit button", () => {
    render(<LoginPage />);
    const button = screen.getByRole("button", { name: /entrar no registro/i });
    expect(button).toBeInTheDocument();
    expect(button).not.toBeDisabled();
  });

  it("renders branding text", () => {
    render(<LoginPage />);
    expect(screen.getByText(/registro/i)).toBeInTheDocument();
    expect(screen.getByText(/acesse sua empresa/i)).toBeInTheDocument();
  });

  it("submits the form and redirects on success", async () => {
    mockLoginAction.mockResolvedValue({ ok: true });
    render(<LoginPage />);

    fireEvent.change(screen.getByLabelText(/e-mail/i), {
      target: { value: "user@example.com" },
    });
    fireEvent.change(screen.getByLabelText(/senha/i), {
      target: { value: "password123" },
    });
    fireEvent.click(screen.getByRole("button", { name: /entrar no registro/i }));

    await waitFor(() => {
      expect(mockLoginAction).toHaveBeenCalledWith("user@example.com", "password123", undefined);
    });
    await waitFor(() => {
      expect(mockPush).toHaveBeenCalledWith("/dashboard");
    });
  });

  it("shows error message on login failure", async () => {
    mockLoginAction.mockResolvedValue({ ok: false, error: "E-mail ou senha invalidos." });
    render(<LoginPage />);

    fireEvent.change(screen.getByLabelText(/e-mail/i), {
      target: { value: "bad@example.com" },
    });
    fireEvent.change(screen.getByLabelText(/senha/i), {
      target: { value: "wrong" },
    });
    fireEvent.click(screen.getByRole("button", { name: /entrar no registro/i }));

    await waitFor(() => {
      expect(screen.getByText(/invalidos/i)).toBeInTheDocument();
    });
  });

  it("shows tenant selector for multi-tenant users", async () => {
    mockLoginAction.mockResolvedValue({
      ok: false,
      multi_tenant: true,
      tenants: [
        { id: 1, name: "Hotel Alpha" },
        { id: 2, name: "Hotel Beta" },
      ],
    });
    render(<LoginPage />);

    fireEvent.change(screen.getByLabelText(/e-mail/i), {
      target: { value: "multi@example.com" },
    });
    fireEvent.change(screen.getByLabelText(/senha/i), {
      target: { value: "pass" },
    });
    fireEvent.click(screen.getByRole("button", { name: /entrar no registro/i }));

    await waitFor(() => {
      expect(screen.getByText("Hotel Alpha")).toBeInTheDocument();
      expect(screen.getByText("Hotel Beta")).toBeInTheDocument();
    });

    // Button should be disabled until tenant is selected
    expect(screen.getByRole("button", { name: /entrar no registro/i })).toBeDisabled();

    // Select a tenant
    fireEvent.click(screen.getByLabelText("Hotel Alpha"));
    expect(screen.getByRole("button", { name: /entrar no registro/i })).not.toBeDisabled();
  });
});
