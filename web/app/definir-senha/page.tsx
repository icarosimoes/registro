"use client";

import { useState, useTransition } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { setPasswordAction } from "@/app/actions";

export default function SetPasswordPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const token = searchParams.get("token") ?? "";
  const [isPending, startTransition] = useTransition();
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    if (password.length < 8) {
      setError("A senha deve ter pelo menos 8 caracteres.");
      return;
    }
    if (password !== confirm) {
      setError("As senhas não coincidem.");
      return;
    }
    if (!token) {
      setError("Token de convite não encontrado.");
      return;
    }
    startTransition(async () => {
      const result = await setPasswordAction(token, password);
      if (result.ok) {
        setSuccess(true);
        setTimeout(() => router.push("/login"), 2000);
      } else {
        setError(result.error ?? "Erro ao definir senha.");
      }
    });
  }

  if (success) {
    return (
      <main className="auth-page">
        <div className="auth-card">
          <h1>Senha definida!</h1>
          <p>Redirecionando para o login…</p>
        </div>
      </main>
    );
  }

  return (
    <main className="auth-page">
      <div className="auth-card">
        <h1>Definir senha</h1>
        <p>Crie uma senha para acessar o sistema.</p>
        <form onSubmit={handleSubmit}>
          <label>
            Nova senha
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              minLength={8}
              placeholder="Mínimo 8 caracteres"
              autoFocus
            />
          </label>
          <label>
            Confirmar senha
            <input
              type="password"
              value={confirm}
              onChange={(e) => setConfirm(e.target.value)}
              required
              minLength={8}
              placeholder="Repita a senha"
            />
          </label>
          {error && <p className="auth-error">{error}</p>}
          <button type="submit" disabled={isPending}>
            {isPending ? "Salvando…" : "Definir senha"}
          </button>
        </form>
      </div>
    </main>
  );
}
