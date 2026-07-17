import { useEffect, useState } from "react"
import { Outlet } from "react-router"
import { ContentContext, DEFAULT_CONTENT, loadContent, saveContent, resetContent, SiteContent } from "./store/content"
import { isWpConfigured, fetchWpContent, saveWpContent } from "./store/wp"

export default function Root() {
  const [content, setContentState] = useState<SiteContent>(loadContent)

  // Com WordPress configurado, o CMS é a fonte da verdade: busca o conteúdo
  // publicado ao montar. Sem WP (ou offline), permanece o fallback local.
  useEffect(() => {
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
