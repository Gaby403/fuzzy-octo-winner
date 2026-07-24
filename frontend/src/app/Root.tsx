import { useEffect, useState } from "react"
import { Outlet } from "react-router"
import { ContentContext, fetchContent, DEFAULT_CONTENT, SiteContent } from "./store/content"

/**
 * Loads the site content from WordPress once, then provides it to the whole
 * app through context. Also wires the WordPress-managed favicon and site
 * title into the document head so both are editable from the CMS.
 */
export default function Root() {
  const [content, setContent] = useState<SiteContent>(DEFAULT_CONTENT)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    let alive = true
    fetchContent().then(c => {
      if (!alive) return
      setContent(c)
      setLoading(false)
    })
    return () => {
      alive = false
    }
  }, [])

  // Document title from the CMS.
  useEffect(() => {
    if (content.site.title) document.title = content.site.title
  }, [content.site.title])

  // Favicon managed from the CMS.
  useEffect(() => {
    const url = content.site.faviconUrl
    if (!url) return
    let link = document.querySelector<HTMLLinkElement>('link[rel="icon"]')
    if (!link) {
      link = document.createElement("link")
      link.rel = "icon"
      document.head.appendChild(link)
    }
    link.href = url
  }, [content.site.faviconUrl])

  return (
    <ContentContext.Provider value={{ content, loading }}>
      <Outlet />
    </ContentContext.Provider>
  )
}
