export type HistoryEntry = {
  action: string;
  user: string;
  date: string;
  changes?: string;
};

export type ModuleRecord = {
  id: number;
  title: string;
  category: string;
  owner: string;
  status: string;
  updatedAt: string;
  description?: string;
  history?: HistoryEntry[];
};

export type ModuleDefinition = {
  slug: string;
  title: string;
  description: string;
  singular: string;
  action: string;
  layout?: "table" | "cards" | "settings" | "profile";
  records: ModuleRecord[];
};

const today = "19/06/2026";

export const moduleDefinitions: Record<string, ModuleDefinition> = {
  ocorrencias: {
    slug: "ocorrencias", title: "Ocorrências", singular: "ocorrência", action: "Nova ocorrência",
    description: "Registre, atribua e acompanhe situações da operação.",
    records: [
      { id: 1048, title: "Revisar vistoria do apartamento 302", category: "Governança", owner: "Marina Costa", status: "Em andamento", updatedAt: "há 12 min" },
      { id: 1047, title: "Anexo pendente no diário de obra", category: "Engenharia", owner: "Rafael Lima", status: "Aguardando", updatedAt: "há 38 min" },
      { id: 1046, title: "Validar ocorrência do turno da manhã", category: "Operação", owner: "Ana Souza", status: "Em andamento", updatedAt: "há 1 h" },
      { id: 1045, title: "Ata da reunião semanal", category: "Administração", owner: "Carlos Reis", status: "Concluído", updatedAt: "ontem" },
    ],
  },
  reunioes: {
    slug: "reunioes", title: "Reuniões", singular: "reunião", action: "Agendar reunião",
    description: "Organize pautas, participantes, decisões e atas.",
    records: [
      { id: 312, title: "Alinhamento operacional semanal", category: "Operação", owner: "Ícaro Simoes", status: "Agendada", updatedAt: today },
      { id: 311, title: "Comitê de segurança", category: "Governança", owner: "Marina Costa", status: "Em andamento", updatedAt: "18/06/2026" },
      { id: 310, title: "Revisão de indicadores", category: "Gestão", owner: "Carlos Reis", status: "Concluído", updatedAt: "17/06/2026" },
    ],
  },
  "relatorios-turno": {
    slug: "relatorios-turno", title: "Relatórios de turno", singular: "relatório", action: "Novo relatório",
    description: "Consolide ocorrências, equipe, manutenção e passagem de turno.",
    records: [
      { id: 821, title: "Turno manhã — Bloco A", category: "Manhã", owner: "Ana Souza", status: "Em andamento", updatedAt: today },
      { id: 820, title: "Turno noite — Bloco B", category: "Noite", owner: "Rafael Lima", status: "Aguardando", updatedAt: "18/06/2026" },
      { id: 819, title: "Turno tarde — Geral", category: "Tarde", owner: "Marina Costa", status: "Concluído", updatedAt: "18/06/2026" },
    ],
  },
  inspecoes: {
    slug: "inspecoes", title: "Inspeções", singular: "inspeção", action: "Nova inspeção",
    description: "Planeje checklists, responsáveis, evidências e resultados.",
    records: [
      { id: 633, title: "Áreas comuns — Torre 1", category: "Predial", owner: "Marina Costa", status: "Em andamento", updatedAt: today },
      { id: 632, title: "Apartamento 302", category: "Vistoria", owner: "Rafael Lima", status: "Aguardando", updatedAt: today },
      { id: 631, title: "Equipamentos de emergência", category: "Segurança", owner: "Ana Souza", status: "Concluído", updatedAt: "17/06/2026" },
    ],
  },
  "diarios-obra": {
    slug: "diarios-obra", title: "Diário de obra", singular: "registro diário", action: "Novo registro",
    description: "Registre atividades, equipes, clima, equipamentos e evidências.",
    records: [
      { id: 177, title: "Reforma do hall principal", category: "Civil", owner: "Rafael Lima", status: "Em andamento", updatedAt: today },
      { id: 176, title: "Adequação elétrica — subsolo", category: "Elétrica", owner: "Carlos Reis", status: "Aguardando", updatedAt: "18/06/2026" },
      { id: 175, title: "Pintura da fachada norte", category: "Acabamento", owner: "Ana Souza", status: "Concluído", updatedAt: "17/06/2026" },
    ],
  },
  manutencao: {
    slug: "manutencao", title: "Manutenção", singular: "ordem", action: "Nova ordem",
    description: "Acompanhe solicitações preventivas e corretivas.",
    records: [
      { id: 490, title: "Revisão da bomba d’água", category: "Preventiva", owner: "Carlos Reis", status: "Agendada", updatedAt: today },
      { id: 489, title: "Iluminação do estacionamento", category: "Corretiva", owner: "Rafael Lima", status: "Em andamento", updatedAt: today },
      { id: 488, title: "Teste do gerador", category: "Preventiva", owner: "Ana Souza", status: "Concluído", updatedAt: "16/06/2026" },
    ],
  },
  cadastros: {
    slug: "cadastros", title: "Cadastros", singular: "cadastro", action: "Novo cadastro",
    description: "Gerencie setores, locais, funções e procedimentos.",
    records: [
      { id: 51, title: "Governança", category: "Setor", owner: "Administração", status: "Ativo", updatedAt: today },
      { id: 50, title: "Bloco administrativo", category: "Local", owner: "Administração", status: "Ativo", updatedAt: "18/06/2026" },
      { id: 49, title: "Supervisor de operação", category: "Função", owner: "Recursos humanos", status: "Ativo", updatedAt: "17/06/2026" },
    ],
  },
  usuarios: {
    slug: "usuarios", title: "Usuários e acesso", singular: "usuário", action: "Convidar usuário",
    description: "Controle pessoas, papéis e permissões da empresa.",
    records: [
      { id: 11, title: "Ícaro Demonstração", category: "Administrador", owner: "icaro@registro.local", status: "Ativo", updatedAt: today },
      { id: 12, title: "Marina Costa", category: "Gestor", owner: "marina@registro.local", status: "Ativo", updatedAt: "18/06/2026" },
      { id: 13, title: "Rafael Lima", category: "Operador", owner: "rafael@registro.local", status: "Pendente", updatedAt: "17/06/2026" },
    ],
  },
  mural: {
    slug: "mural", title: "Mural de avisos", singular: "aviso", action: "Publicar aviso", layout: "cards",
    description: "Comunique mudanças, orientações e informações para a equipe.",
    records: [
      { id: 9, title: "Checklist de fechamento atualizado", category: "Operação", owner: "Marina Costa", status: "Publicado", updatedAt: today, description: "Confira as novas etapas antes de concluir o turno." },
      { id: 8, title: "Inspeções da próxima semana", category: "Governança", owner: "Carlos Reis", status: "Publicado", updatedAt: "18/06/2026", description: "A escala já está disponível para consulta." },
      { id: 7, title: "Manutenção programada", category: "Infraestrutura", owner: "Rafael Lima", status: "Rascunho", updatedAt: "17/06/2026", description: "O gerador será testado na próxima segunda-feira." },
    ],
  },
  configuracoes: {
    slug: "configuracoes", title: "Configurações", singular: "preferência", action: "Salvar alterações", layout: "settings",
    description: "Personalize notificações, idioma e experiência da empresa.", records: [],
  },
  "minha-conta": {
    slug: "minha-conta", title: "Minha conta", singular: "perfil", action: "Salvar perfil", layout: "profile",
    description: "Atualize seus dados pessoais e preferências de acesso.", records: [],
  },
};

export const navigationModules = [
  "ocorrencias", "reunioes", "relatorios-turno", "inspecoes", "diarios-obra", "manutencao",
];
