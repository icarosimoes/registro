import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen } from "@testing-library/react";

// Mock the loginAction (form action)
vi.mock("@/lib/actions", () => ({
  loginAction: vi.fn(),
}));

import LoginPage from "@/app/(auth)/login/page";

describe("Admin LoginPage", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("renders email and password fields", async () => {
    const page = await LoginPage({ searchParams: Promise.resolve({}) });
    render(page);

    expect(screen.getByLabelText(/e-mail/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/senha/i)).toBeInTheDocument();
  });

  it("renders the submit button", async () => {
    const page = await LoginPage({ searchParams: Promise.resolve({}) });
    render(page);

    expect(screen.getByRole("button", { name: /entrar/i })).toBeInTheDocument();
  });

  it("renders platform branding", async () => {
    const page = await LoginPage({ searchParams: Promise.resolve({}) });
    render(page);

    expect(screen.getByText(/painel da plataforma/i)).toBeInTheDocument();
    expect(screen.getByText(/acesso restrito a administradores/i)).toBeInTheDocument();
  });

  it("shows error message when error query param is present", async () => {
    const page = await LoginPage({ searchParams: Promise.resolve({ error: "1" }) });
    render(page);

    expect(screen.getByText(/e-mail ou senha inválidos/i)).toBeInTheDocument();
  });

  it("does not show error when no error param", async () => {
    const page = await LoginPage({ searchParams: Promise.resolve({}) });
    render(page);

    expect(screen.queryByText(/e-mail ou senha inválidos/i)).not.toBeInTheDocument();
  });
});
