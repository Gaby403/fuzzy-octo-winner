import { useRef, useState } from "react"
import { useNavigate } from "react-router"
import { useContent, type SiteContent } from "../store/content"
import { isWpConfigured, loginWp, getWpAuth, clearWpAuth } from "../store/wp"
import { RED, WHITE, BLACK } from "../constants"

const MUTED = "rgba(239,239,239,0.40)"
const BORDER = "rgba(239,239,239,0.10)"
const SURFACE = "#1A1A1A"
const BODY_FONT = '"Be Vietnam Pro", sans-serif'
const HEAD_FONT = '"Roboto Condensed", sans-serif'

const INPUT_BASE: React.CSSProperties = {
  background: SURFACE,
  border: `1px solid ${BORDER}`,
  borderRadius: 6,
  color: WHITE,
  fontSize: 13,
  fontFamily: BODY_FONT,
  padding: "10px 14px",
  outline: "none",
}

// ── Reusable field components ─────────────────────────────────────────────────

function Field({ label, value, onChange, textarea = false, rows = 3 }: {
  label: string; value: string; onChange: (v: string) => void; textarea?: boolean; rows?: number
}) {
  return (
    <div style={{ display: "flex", flexDirection: "column", gap: 6 }}>
      <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED, fontFamily: BODY_FONT }}>
        {label.toUpperCase()}
      </label>
      {textarea ? (
        <textarea
          value={value}
          onChange={e => onChange(e.target.value)}
          rows={rows}
          style={{ ...INPUT_BASE, resize: "vertical", lineHeight: 1.6 }}
          onFocus={e => (e.target.style.borderColor = RED)}
          onBlur={e => (e.target.style.borderColor = BORDER)}
        />
      ) : (
        <input
          value={value}
          onChange={e => onChange(e.target.value)}
          style={INPUT_BASE}
          onFocus={e => (e.target.style.borderColor = RED)}
          onBlur={e => (e.target.style.borderColor = BORDER)}
        />
      )}
    </div>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  const [open, setOpen] = useState(true)
  return (
    <div style={{ border: "1px solid rgba(239,239,239,0.08)", borderRadius: 10, overflow: "hidden", marginBottom: 16 }}>
      <button
        onClick={() => setOpen(o => !o)}
        style={{ width: "100%", background: "#141414", border: "none", padding: "16px 24px", display: "flex", alignItems: "center", justifyContent: "space-between", cursor: "pointer", fontFamily: HEAD_FONT, fontWeight: 700, fontSize: 16, letterSpacing: "-0.02em", textTransform: "uppercase", color: WHITE }}
      >
        {title}
        <span style={{ fontSize: 18, color: open ? RED : MUTED, transition: "transform 0.2s, color 0.2s", display: "inline-block", transform: open ? "rotate(45deg)" : "none" }}>+</span>
      </button>
      {open && (
        <div style={{ padding: "24px", display: "flex", flexDirection: "column", gap: 20, background: "#0D0D0D" }}>
          {children}
        </div>
      )}
    </div>
  )
}

// ── Login gate ────────────────────────────────────────────────────────────────

function LoginGate({ onLogin }: { onLogin: () => void }) {
  const [user, setUser] = useState("")
  const [pw, setPw] = useState("")
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState(false)

  async function submit(e: React.FormEvent) {
    e.preventDefault()
    if (isWpConfigured) {
      // Autentica no WordPress com usuário + Application Password
      setBusy(true)
      const ok = await loginWp(user.trim(), pw)
      setBusy(false)
      if (ok) { onLogin() } else { setError(true); setPw("") }
    } else {
      // Modo standalone (sem WP): senha local
      if (pw === "tabi2025") { onLogin() } else { setError(true); setPw("") }
    }
  }

  return (
    <div style={{ minHeight: "100svh", background: BLACK, display: "flex", alignItems: "center", justifyContent: "center", fontFamily: BODY_FONT }}>
      <form onSubmit={submit} style={{ width: "min(360px, 90vw)", display: "flex", flexDirection: "column", gap: 20 }}>
        <div>
          <div style={{ fontFamily: HEAD_FONT, fontWeight: 900, fontSize: 32, letterSpacing: "-0.05em", textTransform: "uppercase", color: WHITE, marginBottom: 4 }}>
            STUDIO <span style={{ color: RED }}>TABI</span>
          </div>
          <p style={{ fontSize: 11, letterSpacing: "0.12em", color: "rgba(239,239,239,0.35)", margin: 0 }}>PAINEL ADMINISTRATIVO</p>
        </div>
        {isWpConfigured && (
          <div style={{ display: "flex", flexDirection: "column", gap: 8 }}>
            <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED }}>USUÁRIO WORDPRESS</label>
            <input
              value={user}
              onChange={e => { setUser(e.target.value); setError(false) }}
              placeholder="admin"
              style={{ ...INPUT_BASE, border: `1px solid ${error ? RED : "rgba(239,239,239,0.12)"}`, borderRadius: 8, fontSize: 14, padding: "12px 16px" }}
              autoFocus
            />
          </div>
        )}
        <div style={{ display: "flex", flexDirection: "column", gap: 8 }}>
          <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED }}>
            {isWpConfigured ? "APPLICATION PASSWORD" : "SENHA"}
          </label>
          <input
            type="password"
            value={pw}
            onChange={e => { setPw(e.target.value); setError(false) }}
            placeholder="••••••••"
            style={{ ...INPUT_BASE, border: `1px solid ${error ? RED : "rgba(239,239,239,0.12)"}`, borderRadius: 8, fontSize: 14, padding: "12px 16px" }}
            autoFocus={!isWpConfigured}
          />
          {error && (
            <p style={{ margin: 0, fontSize: 11, color: RED }}>
              {isWpConfigured ? "Credenciais recusadas pelo WordPress." : "Senha incorreta."}
            </p>
          )}
          {isWpConfigured && (
            <p style={{ margin: 0, fontSize: 10, lineHeight: 1.5, color: "rgba(239,239,239,0.30)" }}>
              Gere uma Application Password no WordPress em Usuários → Perfil → Application Passwords.
            </p>
          )}
        </div>
        <button type="submit" disabled={busy} style={{ background: RED, border: "none", borderRadius: 8, color: WHITE, fontFamily: BODY_FONT, fontWeight: 700, fontSize: 12, letterSpacing: "0.12em", padding: "13px", cursor: busy ? "wait" : "pointer", opacity: busy ? 0.6 : 1 }}>
          {busy ? "VERIFICANDO…" : "ENTRAR"}
        </button>
      </form>
    </div>
  )
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function updateArr<T>(arr: T[], idx: number, patch: Partial<T>): T[] {
  const next = [...arr]
  next[idx] = { ...next[idx], ...patch }
  return next
}

// ── Main Admin Panel ──────────────────────────────────────────────────────────

export default function Admin() {
  const [authed, setAuthed] = useState(() =>
    isWpConfigured ? getWpAuth() !== null : sessionStorage.getItem("tabi_admin") === "1"
  )
  const navigate = useNavigate()
  const { content, setContent, persist, reset } = useContent()
  const [saved, setSaved] = useState(false)
  const [saving, setSaving] = useState(false)
  const saveTimer = useRef<ReturnType<typeof setTimeout> | null>(null)

  function login() {
    if (!isWpConfigured) sessionStorage.setItem("tabi_admin", "1")
    setAuthed(true)
  }

  if (!authed) return <LoginGate onLogin={login} />

  function update(path: string[], value: unknown) {
    if (path.length === 1) {
      setContent({ ...content, [path[0]]: value } as SiteContent)
    } else {
      const top = path[0] as keyof SiteContent
      setContent({ ...content, [top]: { ...(content[top] as object), [path[1]]: value } } as SiteContent)
    }
  }

  async function save() {
    if (saveTimer.current) clearTimeout(saveTimer.current)
    setSaving(true)
    try {
      await persist()
      setSaved(true)
      saveTimer.current = setTimeout(() => setSaved(false), 2500)
    } catch (err) {
      alert(err instanceof Error ? err.message : "Falha ao salvar.")
      if (isWpConfigured && getWpAuth() === null) { clearWpAuth(); setAuthed(false) }
    } finally {
      setSaving(false)
    }
  }

  async function doReset() {
    try {
      await reset()
      setSaved(false)
    } catch (err) {
      alert(err instanceof Error ? err.message : "Falha ao resetar.")
    }
  }

  return (
    <>
      <style>{`
        .admin-ghost-btn:hover { color: ${RED} !important; border-color: ${RED} !important; }
        .admin-add-btn:hover   { color: ${RED} !important; border-color: ${RED} !important; }
      `}</style>

      <div style={{ minHeight: "100svh", background: BLACK, fontFamily: BODY_FONT, color: WHITE }}>

        {/* Top bar */}
        <div style={{ position: "sticky", top: 0, zIndex: 50, background: "#0A0A0A", borderBottom: "1px solid rgba(239,239,239,0.08)", padding: "0 clamp(16px,3vw,40px)", display: "flex", alignItems: "center", justifyContent: "space-between", height: 56 }}>
          <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
            <span style={{ fontFamily: HEAD_FONT, fontWeight: 900, fontSize: 18, letterSpacing: "-0.04em", textTransform: "uppercase" }}>
              STUDIO <span style={{ color: RED }}>TABI</span>
            </span>
            <span style={{ fontSize: 9, letterSpacing: "0.14em", color: "rgba(239,239,239,0.30)", fontWeight: 600 }}>ADMIN</span>
          </div>
          <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
            {saving && <span style={{ fontSize: 10, color: MUTED, letterSpacing: "0.1em", fontWeight: 600 }}>SALVANDO…</span>}
            {saved && !saving && <span style={{ fontSize: 10, color: "#3DBF72", letterSpacing: "0.1em", fontWeight: 600 }}>✓ SALVO</span>}
            <button onClick={() => { if (confirm("Resetar todo o conteúdo para os valores originais?")) { void doReset() } }}
              style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.1em", color: "rgba(239,239,239,0.35)", background: "transparent", border: "1px solid rgba(239,239,239,0.10)", borderRadius: 6, padding: "7px 14px", cursor: "pointer" }}>
              RESETAR
            </button>
            <button onClick={() => { void save().then(() => navigate("/")) }}
              style={{ fontSize: 10, fontWeight: 700, letterSpacing: "0.1em", color: WHITE, background: RED, border: "none", borderRadius: 6, padding: "7px 16px", cursor: "pointer" }}>
              VER SITE →
            </button>
          </div>
        </div>

        <div style={{ maxWidth: 860, margin: "0 auto", padding: "32px clamp(16px,3vw,40px) 80px" }}>

          {/* ── HERO ── */}
          <Section title="Hero">
            {content.hero.titleLines.map((line, i) => (
              <Field key={i} label={`Linha ${i + 1} do título`} value={line}
                onChange={v => { const lines = [...content.hero.titleLines]; lines[i] = v; update(["hero", "titleLines"], lines) }} />
            ))}
            <Field label="Descrição" value={content.hero.description} textarea rows={2}
              onChange={v => update(["hero", "description"], v)} />
          </Section>

          {/* ── SOBRE ── */}
          <Section title="Sobre">
            <Field label="Parágrafo 1" value={content.about.paragraph1} textarea rows={4}
              onChange={v => update(["about", "paragraph1"], v)} />
            <Field label="Parágrafo 2" value={content.about.paragraph2} textarea rows={4}
              onChange={v => update(["about", "paragraph2"], v)} />

            <div style={{ borderTop: "1px solid rgba(239,239,239,0.07)", paddingTop: 20 }}>
              <p style={{ fontSize: 10, letterSpacing: "0.12em", color: MUTED, margin: "0 0 16px", fontWeight: 600 }}>STATS</p>
              <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
                {content.about.stats.map((stat, i) => (
                  <div key={i} style={{ display: "flex", flexDirection: "column", gap: 8 }}>
                    <Field label={`Valor ${i + 1}`} value={String(stat.numeric)}
                      onChange={v => update(["about", "stats"], updateArr(content.about.stats, i, { numeric: Number(v) || 0 }))} />
                    <Field label={`Label ${i + 1}`} value={stat.label}
                      onChange={v => update(["about", "stats"], updateArr(content.about.stats, i, { label: v }))} />
                  </div>
                ))}
              </div>
            </div>

            <div style={{ borderTop: "1px solid rgba(239,239,239,0.07)", paddingTop: 20 }}>
              <p style={{ fontSize: 10, letterSpacing: "0.12em", color: MUTED, margin: "0 0 16px", fontWeight: 600 }}>PILARES</p>
              {content.about.pillars.map((p, i) => (
                <div key={i} style={{ marginBottom: 20, paddingBottom: 20, borderBottom: i < content.about.pillars.length - 1 ? "1px solid rgba(239,239,239,0.05)" : "none", display: "flex", flexDirection: "column", gap: 8 }}>
                  <Field label={`Título ${i + 1}`} value={p.title}
                    onChange={v => update(["about", "pillars"], updateArr(content.about.pillars, i, { title: v }))} />
                  <Field label={`Texto ${i + 1}`} value={p.body} textarea
                    onChange={v => update(["about", "pillars"], updateArr(content.about.pillars, i, { body: v }))} />
                </div>
              ))}
            </div>
          </Section>

          {/* ── SERVIÇOS ── */}
          <Section title="Serviços">
            {content.services.map((s, i) => (
              <div key={i} style={{ paddingBottom: 20, borderBottom: i < content.services.length - 1 ? "1px solid rgba(239,239,239,0.05)" : "none", display: "flex", flexDirection: "column", gap: 8 }}>
                <Field label={`${s.num} — Título`} value={s.title}
                  onChange={v => update(["services"], updateArr(content.services, i, { title: v }))} />
                <Field label="Descrição" value={s.body} textarea
                  onChange={v => update(["services"], updateArr(content.services, i, { body: v }))} />
              </div>
            ))}
          </Section>

          {/* ── PROJETOS ── */}
          <Section title="Projetos">
            {content.projects.map((proj, i) => {
              function updateProj(patch: Partial<typeof proj>) {
                update(["projects"], updateArr(content.projects, i, patch))
              }
              function updateDetail(patch: Partial<typeof proj.detail>) {
                updateProj({ detail: { ...proj.detail, ...patch } })
              }
              function handleImageFile(e: React.ChangeEvent<HTMLInputElement>) {
                const file = e.target.files?.[0]; if (!file) return
                const reader = new FileReader()
                reader.onload = ev => updateProj({ imageUrl: ev.target?.result as string })
                reader.readAsDataURL(file)
              }

              return (
                <div key={proj.id} style={{ paddingBottom: 24, borderBottom: i < content.projects.length - 1 ? "1px solid rgba(239,239,239,0.07)" : "none", display: "flex", flexDirection: "column", gap: 12 }}>

                  <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between" }}>
                    <p style={{ margin: 0, fontSize: 10, fontWeight: 700, letterSpacing: "0.14em", color: proj.accent }}>{proj.id} — {proj.name}</p>
                    <button className="admin-ghost-btn"
                      onClick={() => { if (confirm(`Remover o projeto "${proj.name}"?`)) update(["projects"], content.projects.filter((_, idx) => idx !== i)) }}
                      style={{ fontSize: 10, fontWeight: 600, color: MUTED, background: "transparent", border: "1px solid rgba(239,239,239,0.08)", borderRadius: 5, padding: "4px 10px", cursor: "pointer", letterSpacing: "0.08em", transition: "color 0.15s, border-color 0.15s" }}>
                      REMOVER
                    </button>
                  </div>

                  {/* Image */}
                  <div style={{ display: "flex", flexDirection: "column", gap: 8 }}>
                    <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED }}>IMAGEM DO PROJETO</label>
                    {proj.imageUrl && (
                      <div style={{ position: "relative", borderRadius: 6, overflow: "hidden", height: 120 }}>
                        <img src={proj.imageUrl} alt="" style={{ width: "100%", height: "100%", objectFit: "cover" }} />
                        <button onClick={() => updateProj({ imageUrl: undefined })}
                          style={{ position: "absolute", top: 8, right: 8, background: "rgba(0,0,0,0.7)", border: "none", borderRadius: 4, color: WHITE, fontSize: 10, padding: "4px 8px", cursor: "pointer" }}>
                          ✕ REMOVER
                        </button>
                      </div>
                    )}
                    <div style={{ display: "grid", gridTemplateColumns: "1fr auto", gap: 8, alignItems: "end" }}>
                      <Field label="URL da imagem" value={proj.imageUrl ?? ""}
                        onChange={v => updateProj({ imageUrl: v || undefined })} />
                      <div style={{ display: "flex", flexDirection: "column", gap: 6 }}>
                        <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED }}>OU UPLOAD</label>
                        <label style={{ display: "flex", alignItems: "center", justifyContent: "center", background: SURFACE, border: "1px dashed rgba(239,239,239,0.20)", borderRadius: 6, color: "rgba(239,239,239,0.50)", fontSize: 11, padding: "10px 16px", cursor: "pointer", whiteSpace: "nowrap" }}>
                          📁 Escolher arquivo
                          <input type="file" accept="image/*" onChange={handleImageFile} style={{ display: "none" }} />
                        </label>
                      </div>
                    </div>
                  </div>

                  {/* Featured toggle */}
                  <div style={{ display: "flex", alignItems: "center", gap: 12 }}>
                    <label style={{ fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: MUTED }}>DESTAQUE (card grande)</label>
                    <button onClick={() => updateProj({ featured: !proj.featured })}
                      style={{ padding: "5px 14px", borderRadius: 20, border: "none", fontSize: 10, fontWeight: 700, letterSpacing: "0.10em", cursor: "pointer", background: proj.featured ? RED : "#2A2A2A", color: proj.featured ? WHITE : MUTED, transition: "background 0.2s, color 0.2s" }}>
                      {proj.featured ? "SIM" : "NÃO"}
                    </button>
                  </div>

                  {/* Core fields */}
                  <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
                    <Field label="Nome" value={proj.name} onChange={v => updateProj({ name: v })} />
                    <Field label="Categoria" value={proj.category} onChange={v => updateProj({ category: v })} />
                    <Field label="Ano" value={proj.year} onChange={v => updateProj({ year: v })} />
                    <Field label="Cliente (detalhe)" value={proj.detail.client} onChange={v => updateDetail({ client: v })} />
                    <Field label="Duração" value={proj.detail.duration} onChange={v => updateDetail({ duration: v })} />
                    <Field label="Cor de destaque (hex)" value={proj.accent} onChange={v => updateProj({ accent: v })} />
                  </div>

                  <Field label="Desafio" value={proj.detail.challenge} textarea rows={4} onChange={v => updateDetail({ challenge: v })} />
                  <Field label="Solução" value={proj.detail.solution} textarea rows={4} onChange={v => updateDetail({ solution: v })} />

                  <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 8 }}>
                    {proj.detail.results.map((r, ri) => (
                      <div key={ri} style={{ display: "flex", flexDirection: "column", gap: 6 }}>
                        <Field label={`Resultado ${ri + 1} — valor`} value={r.value}
                          onChange={v => updateDetail({ results: updateArr(proj.detail.results, ri, { value: v }) })} />
                        <Field label={`Resultado ${ri + 1} — label`} value={r.label}
                          onChange={v => updateDetail({ results: updateArr(proj.detail.results, ri, { label: v }) })} />
                      </div>
                    ))}
                  </div>
                </div>
              )
            })}

            <button className="admin-add-btn"
              onClick={() => {
                const nextId = String(content.projects.reduce((max, p) => Math.max(max, parseInt(p.id, 10) || 0), 0) + 1).padStart(2, "0")
                update(["projects"], [...content.projects, {
                  id: nextId, name: "Novo Projeto", category: "Categoria", year: String(new Date().getFullYear()),
                  bg: "linear-gradient(135deg,#0A0A14 0%,#14140A 100%)", accent: RED, featured: false,
                  detail: {
                    client: "Cliente", scope: ["Design"], duration: "8 semanas",
                    challenge: "Descreva o desafio aqui.", solution: "Descreva a solução aqui.",
                    results: [
                      { label: "Métrica 1", value: "+0%" }, { label: "Métrica 2", value: "+0%" },
                      { label: "Métrica 3", value: "0" },   { label: "Métrica 4", value: "0" },
                    ],
                    mockupLines: ["TELA 1", "TELA 2", "TELA 3", "TELA 4"],
                  },
                }])
              }}
              style={{ width: "100%", background: "transparent", border: "1px dashed rgba(239,239,239,0.15)", borderRadius: 8, color: MUTED, fontFamily: BODY_FONT, fontWeight: 600, fontSize: 11, letterSpacing: "0.12em", padding: "14px", cursor: "pointer", marginTop: 8, transition: "color 0.15s, border-color 0.15s" }}>
              + ADICIONAR PROJETO
            </button>
          </Section>

          {/* ── FAQ ── */}
          <Section title="FAQ">
            {content.faq.map((item, i) => (
              <div key={i} style={{ paddingBottom: 20, borderBottom: i < content.faq.length - 1 ? "1px solid rgba(239,239,239,0.05)" : "none", display: "flex", flexDirection: "column", gap: 8 }}>
                <Field label={`Pergunta ${i + 1}`} value={item.q}
                  onChange={v => update(["faq"], updateArr(content.faq, i, { q: v }))} />
                <Field label="Resposta" value={item.a} textarea rows={3}
                  onChange={v => update(["faq"], updateArr(content.faq, i, { a: v }))} />
              </div>
            ))}
          </Section>

          {/* ── FOOTER ── */}
          <Section title="Footer">
            <Field label="Tagline" value={content.footer.tagline} onChange={v => update(["footer", "tagline"], v)} />
            <Field label="E-mail"  value={content.footer.email}   onChange={v => update(["footer", "email"], v)} />
            <Field label="Telefone" value={content.footer.phone}  onChange={v => update(["footer", "phone"], v)} />
            <Field label="Cidade"  value={content.footer.city}    onChange={v => update(["footer", "city"], v)} />
          </Section>

          <button onClick={() => void save()}
            style={{ width: "100%", background: RED, border: "none", borderRadius: 8, color: WHITE, fontFamily: BODY_FONT, fontWeight: 700, fontSize: 12, letterSpacing: "0.14em", padding: "16px", cursor: "pointer", marginTop: 8 }}>
            {saving ? "SALVANDO…" : saved ? "✓ ALTERAÇÕES SALVAS" : "SALVAR ALTERAÇÕES"}
          </button>
          <p style={{ textAlign: "center", fontSize: 10, color: "rgba(239,239,239,0.20)", marginTop: 12 }}>
            {isWpConfigured ? "As alterações são publicadas no WordPress." : "As alterações são salvas localmente no navegador."}
          </p>
        </div>
      </div>
    </>
  )
}
