import { useEffect, useState } from "react"
import { Outlet } from "react-router"
import { ContentContext, DEFAULT_CONTENT, loadContent, saveContent, resetContent, SiteContent } from "./store/content"
import { isWpConfigured, fetchWpContent, saveWpContent, getInjectedContent } from "./store/wp"

// No modo tema unificado, o WordPress injeta o conteúdo inline na página.
// Se existir, ele é a fonte da verdade já na primeira renderização (sem flash
// de conteúdo padrão e sem chamada de rede).
function initialContent(): SiteContent {
  const injected = getInjectedContent()
  if (injected) return { ...DEFAULT_CONTENT, ...injected }
  return loadContent()
}

export default function Root() {
  const [content, setContentState] = useState<SiteContent>(initialContent)

  // Sem conteúdo injetado, mas com WordPress headless configurado, busca o
  // conteúdo publicado via API ao montar. Sem nada disso, usa o fallback local.
  useEffect(() => {
    if (getInjectedContent()) return
    fetchWpContent().then(remote => {
      if (remote) setContentState({ ...DEFAULT_CONTENT, ...remote })
    })
  }, [])

  function setContent(c: SiteContent) {
    setContentState(c)
    if (!isWpConfigured) saveContent(c)
  }

  async function persist() {
    if (isWpConfigured) {
      await saveWpContent(content)
    } else {
      saveContent(content)
    }
  }

  async function reset() {
    resetContent()
    setContentState(DEFAULT_CONTENT)
    if (isWpConfigured) await saveWpContent(DEFAULT_CONTENT)
  }

  return (
    <ContentContext.Provider value={{ content, setContent, persist, reset }}>
      <Outlet />
    </ContentContext.Provider>
  )
}
