import { loginAction } from "@/app/actions";

export default async function LoginPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const { error } = await searchParams;

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
          <p>Entre com as credenciais do ambiente de demonstração.</p>
          {error ? <div className="login-error">E-mail, senha ou empresa inválidos.</div> : null}
          <form action={loginAction}>
            <label>
              Empresa
              <input name="company_slug" required defaultValue="empresa-demo" autoComplete="organization" />
            </label>
            <label>
              E-mail
              <input name="email" type="email" required defaultValue="icaro@registro.local" autoComplete="username" />
            </label>
            <label>
              Senha
              <input name="password" type="password" required defaultValue="Registro@123" autoComplete="current-password" />
            </label>
            <button type="submit">Entrar no Registro</button>
          </form>
          <small>Credenciais fictícias — somente ambiente local.</small>
        </div>
      </section>
    </main>
  );
}
