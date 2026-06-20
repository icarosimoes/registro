"use client";

import { useState, useTransition } from "react";
import { useRouter } from "next/navigation";
import { loginAction } from "@/app/actions";

export default function LoginPage() {
  const router = useRouter();
  const [isPending, startTransition] = useTransition();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [tenants, setTenants] = useState<{ id: number; name: string }[]>([]);
  const [selectedTenant, setSelectedTenant] = useState<number | null>(null);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    startTransition(async () => {
      const result = await loginAction(email, password, selectedTenant ?? undefined);
      if (result.ok) {
        router.push("/dashboard");
        return;
      }
      if (result.multi_tenant && result.tenants) {
        setTenants(result.tenants);
        setSelectedTenant(null);
        return;
      }
      setError(result.error ?? "Falha no login.");
    });
  }

  return (
    <main className="tenant-login-page">
      <section className="tenant-login-copy">
        <span className="tenant-login-logo">R</span>
        <div>
          <p className="eyebrow">REGISTRO</p>
          <h1>Operação organizada, decisões mais rápidas.</h1>
          <p>Ocorrências, inspeções, reuniões e equipes no mesmo lugar.</p>
        </div>
        <small>Plataforma SaaS multitenant</small>
      </section>

      <section className="tenant-login-form-wrap">
        <div className="tenant-login-card">
          <p className="eyebrow">Bem-vindo</p>
          <h2>Acesse sua empresa</h2>
          <p>Entre com seu e-mail e senha.</p>
          {error && <div className="login-error">{error}</div>}
          <form onSubmit={handleSubmit}>
            <label>
              E-mail
              <input
                name="email"
                type="email"
                required
                value={email}
                onChange={(e) => { setEmail(e.target.value); setTenants([]); setSelectedTenant(null); }}
                autoComplete="username"
              />
            </label>
            <label>
              Senha
              <input
                name="password"
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                autoComplete="current-password"
              />
            </label>
            {tenants.length > 1 && (
              <fieldset className="tenant-selector">
                <legend>Selecione a empresa</legend>
                {tenants.map((t) => (
                  <label key={t.id} className={`tenant-option${selectedTenant === t.id ? " selected" : ""}`}>
                    <input
                      type="radio"
                      name="tenant"
                      value={t.id}
                      checked={selectedTenant === t.id}
                      onChange={() => setSelectedTenant(t.id)}
                    />
                    {t.name}
                  </label>
                ))}
              </fieldset>
            )}
            <button type="submit" disabled={isPending || (tenants.length > 1 && !selectedTenant)}>
              {isPending ? "Entrando..." : "Entrar no Registro"}
            </button>
          </form>
        </div>
      </section>
    </main>
  );
}
