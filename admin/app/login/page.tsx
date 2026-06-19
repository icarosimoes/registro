import { loginAction } from "@/app/actions";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ error?: string }> }) {
  const { error } = await searchParams;
  return (
    <main className="login-page">
      <section className="login-card">
        <span className="brand">REGISTRO</span>
        <h1>Painel da plataforma</h1>
        <p>Gerencie empresas, planos e assinaturas.</p>
        {error ? <div className="error">E-mail ou senha inválidos.</div> : null}
        <form action={loginAction}>
          <label>E-mail<input name="email" type="email" required defaultValue="admin@registro.local" /></label>
          <label>Senha<input name="password" type="password" required defaultValue="RegistroAdmin@123" /></label>
          <button type="submit">Entrar</button>
        </form>
        <small>Credenciais demonstrativas — somente ambiente local.</small>
      </section>
    </main>
  );
}
