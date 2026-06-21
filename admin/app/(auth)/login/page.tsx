import { loginAction } from "@/lib/actions";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ error?: string }> }) {
  const { error } = await searchParams;
  return (
    <main className="min-h-screen flex items-center justify-center p-6" style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}>
      <section className="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10">
        <span className="text-xs font-extrabold tracking-[.18em] text-[#1D3461] uppercase">Registro</span>
        <h1 className="text-2xl font-bold text-gray-900 mt-3 mb-1">Painel da plataforma</h1>
        <p className="text-sm text-gray-500">Gerencie empresas, planos e assinaturas.</p>

        {error && (
          <div className="mt-4 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
            E-mail ou senha inválidos.
          </div>
        )}

        <form action={loginAction} className="mt-7 space-y-5">
          <label className="block">
            <span className="text-sm font-semibold text-gray-700">E-mail</span>
            <input
              name="email"
              type="email"
              required
              defaultValue="admin@registro.local"
              className="mt-1.5 w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1D3461]/30 focus:border-[#1D3461]"
            />
          </label>
          <label className="block">
            <span className="text-sm font-semibold text-gray-700">Senha</span>
            <input
              name="password"
              type="password"
              required
              defaultValue="RegistroAdmin@123"
              className="mt-1.5 w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1D3461]/30 focus:border-[#1D3461]"
            />
          </label>
          <button
            type="submit"
            className="w-full rounded-lg py-3 text-sm font-bold text-white transition-colors"
            style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}
          >
            Entrar
          </button>
        </form>
        <p className="mt-5 text-xs text-gray-400 text-center">Credenciais demonstrativas — somente ambiente local.</p>
      </section>
    </main>
  );
}
