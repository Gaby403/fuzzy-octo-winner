import { useRef, useEffect, useState, useCallback } from "react"
import {
  motion,
  AnimatePresence,
  useScroll,
  useTransform,
  useMotionValue,
  useSpring,
  useMotionTemplate,
  useInView,
} from "motion/react"
import { RouterProvider, createBrowserRouter, Link, useNavigate, useParams } from "react-router"
import { useContent, type SiteContent } from "./store/content"
import Root from "./Root"
import Admin from "./pages/Admin"
import { RED, WHITE, BLACK, EASE_OUT_EXPO } from "./constants"

// Tabi brand mark SVG — replaces kanji throughout the site
function TabiMark({ width = 120, color = "#111111", opacity = 1, style }: {
  width?: number | string
  color?: string
  opacity?: number
  style?: React.CSSProperties
}) {
  return (
    <svg
      width={width}
      viewBox="0 0 224 220"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden="true"
      style={{ display: "block", flexShrink: 0, opacity, ...style }}
    >
      <path
        d="M170.261 204.844L103.042 219.81L91.5114 188.161L105.741 185.217V92.4837L109.421 91.5023C101.08 85.1235 90.7799 80.4621 80.4757 77.0273L87.8359 68.6858L58.3952 68.1951L58.1499 85.3688H94.9462L94.4555 129.28L92.2474 169.02C91.2661 185.458 88.0767 204.349 71.8843 213.181C65.9962 216.371 59.6173 217.597 52.9932 218.333C49.3131 206.807 43.9157 196.257 39.4996 189.633C34.8381 199.692 29.9313 209.26 23.5525 218.579C15.4563 208.034 7.11483 197.48 0 191.105C19.3818 160.438 26.7374 135.904 27.4734 94.9371L27.9641 67.9498H2.70781V36.5464H38.5227C36.0694 27.4689 32.1439 19.1273 26.9918 9.80901L55.9418 0C61.3393 9.56822 66.0007 21.0991 70.4168 32.1394C66.0007 33.3661 61.5846 34.5928 57.4138 36.0648L94.7008 36.5555V59.8627C106.722 42.4436 116.781 21.8352 122.669 0.495221C132.969 3.68463 143.769 6.62869 154.314 10.0634C152.351 17.9143 149.898 24.0478 147.444 31.1626L219.815 31.4079V63.5473L189.393 63.302C196.262 69.4354 203.868 75.0782 211.468 80.721C198.715 88.8172 185.222 95.6867 171.728 102.066C172.709 108.935 174.182 116.05 176.635 122.183C183.75 115.559 190.619 108.69 196.017 100.839L221.041 119.975C210.492 131.752 200.433 140.338 189.147 148.925C197.243 162.419 210.492 174.195 223.495 182.532C214.908 192.346 208.529 203.141 202.395 213.445C188.661 205.103 178.357 194.063 170.751 182.041L170.261 204.853V204.844ZM57.1685 117.263C54.7151 138.853 50.0537 164.609 41.9575 184.972C46.1283 184.727 50.5443 184.727 54.4698 183.745C57.6592 182.764 59.8672 179.82 61.0939 176.876C63.7927 158.234 65.0193 137.135 65.2647 117.263H57.1685ZM131.747 63.5382C126.35 72.8611 120.461 81.6933 113.351 89.5441C135.427 83.656 157.508 75.5598 177.135 64.0289L131.747 63.5382ZM138.126 179.579C147.203 177.862 155.295 176.635 165.354 174.427C155.295 155.29 148.185 135.418 144.995 113.583C142.542 114.319 140.334 115.3 138.126 116.282V179.574V179.579Z"
        fill={color}
      />
    </svg>
  )
}
const NAV_LINKS: { label: string; to: string }[] = [
  { label: "TRABALHOS", to: "/#trabalhos" },
  { label: "SERVIÇOS", to: "/servicos" },
  { label: "BLOG", to: "/blog" },
  { label: "SOBRE", to: "/#sobre" },
  { label: "CONTATO", to: "/#contato" },
]

const MOUNTAIN_PATHS = {
  haze:   "M0 900 L0 480 C100 474 188 485 274 469 C360 453 436 473 516 448 C596 422 663 444 735 408 C794 378 838 344 879 303 C919 263 953 232 987 205 C1019 180 1047 187 1074 218 C1103 251 1128 274 1159 295 C1192 318 1218 308 1249 279 C1278 252 1306 255 1334 284 C1365 317 1392 337 1426 351 C1460 365 1487 350 1515 322 C1542 295 1572 306 1600 342 L1600 900 Z",
  far:    "M0 900 L0 488 C108 481 199 493 292 474 C380 456 455 482 542 452 C624 424 688 448 760 411 C819 382 862 349 905 307 C948 265 984 230 1021 201 C1057 173 1087 184 1117 223 C1150 266 1178 296 1217 318 C1257 341 1288 333 1323 301 C1358 270 1387 277 1418 313 C1452 353 1483 375 1519 394 C1551 410 1577 428 1600 445 L1600 900 Z",
  middle: "M0 900 L0 494 C115 487 207 498 304 484 C399 469 479 493 567 464 C649 438 718 458 790 428 C852 403 899 376 947 336 C992 299 1030 260 1069 222 C1106 186 1139 193 1171 235 C1206 282 1238 320 1281 351 C1324 381 1362 371 1404 343 C1445 316 1479 331 1514 371 C1549 411 1576 438 1600 455 L1600 900 Z",
  front:  "M0 900 L0 474 C94 450 174 439 253 452 C330 464 395 489 472 496 C553 502 620 485 684 457 C750 428 804 403 862 423 C926 445 975 486 1042 493 C1114 501 1172 461 1231 431 C1288 402 1337 386 1388 409 C1443 434 1487 459 1534 471 C1560 478 1582 483 1600 487 L1600 900 Z",
}

function lerp(a: number, b: number, t: number) {
  return a + (b - a) * Math.max(0, Math.min(1, t))
}

// Slug amigável para URL a partir de um texto (fallback quando o projeto não tem slug).
function slugify(s: string) {
  return s
    .toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "")
}
function projSlug(p: { slug?: string; name: string }) {
  return p.slug && p.slug.trim() ? p.slug.trim() : slugify(p.name)
}

// ── Responsive CSS injected as a style tag so !important can override inline styles ──
const RESPONSIVE_CSS = `
  /* ── Default: nowrap via class (overridable, unlike inline style) ── */
  .hero-title-line {
    white-space: nowrap;
  }

  /* ── Tablet (768–1100px) ── */
  @media (max-width: 1100px) {
    .hero-sun-anchor {
      left: 75% !important;
      top: 47% !important;
      width: clamp(290px, 43vw, 470px) !important;
    }
    .hero-content {
      width: 54% !important;
      padding-bottom: 130px !important;
    }
  }

  /* ── Mobile (≤ 767px) ── */
  @media (max-width: 767px) {
    .hero-sun-anchor {
      left: 64% !important;
      top: 74% !important;
      width: clamp(200px, 60vw, 290px) !important;
    }
    .hero-content {
      width: 100% !important;
      top: 0 !important;
      bottom: auto !important;
      height: auto !important;
      justify-content: flex-start !important;
      padding: clamp(68px, 10svh, 92px) 24px 24px !important;
      overflow: hidden !important;
    }
    .hero-title-line {
      white-space: normal !important;
    }
    .hero-title {
      font-size: clamp(32px, 9.8vw, 52px) !important;
      line-height: 0.86 !important;
      max-width: 100% !important;
    }
    .hero-cta-secondary {
      display: none !important;
    }
    .hero-desc {
      font-size: clamp(11px, 3vw, 13px) !important;
      margin-top: 18px !important;
      width: 90% !important;
    }
    .hero-mountain {
      left: -10% !important;
      width: 120% !important;
    }
    .hero-mountain-haze   { bottom: 9%  !important; height: 25%   !important; }
    .hero-mountain-far    { bottom: 6%  !important; height: 22%   !important; }
    .hero-mountain-middle { bottom: 2%  !important; height: 18.5% !important; }
    .hero-mountain-front  { bottom: -1px !important; height: 14% !important; }
    .hero-footer {
      padding: 0 20px max(18px, env(safe-area-inset-bottom)) !important;
      font-size: 8px !important;
    }
    .hero-kanji {
      font-size: clamp(60px, 16vw, 88px) !important;
    }
  }

  /* ── Mobile landscape ── */
  @media (max-width: 900px) and (orientation: landscape) {
    .hero-sun-anchor {
      left: 76% !important;
      top: 51% !important;
      width: min(38vw, 300px) !important;
    }
    .hero-content {
      width: 54% !important;
      justify-content: center !important;
      padding: 54px 0 70px 28px !important;
      overflow: visible !important;
    }
    .hero-title-line {
      white-space: nowrap !important;
      word-break: normal !important;
    }
    .hero-mountain-haze   { height: 37% !important; }
    .hero-mountain-far    { height: 33% !important; }
    .hero-mountain-middle { height: 28% !important; }
    .hero-mountain-front  { height: 21% !important; }
  }

  /* ── Narrow phones (≤ 380px) ── */
  @media (max-width: 380px) {
    .hero-sun-anchor {
      top: 76% !important;
      width: 58vw !important;
      left: 62% !important;
    }
    .hero-content {
      padding-top: clamp(56px, 8svh, 70px) !important;
    }
  }
`

export function HomeSite() {
  const { content } = useContent()
  const containerRef = useRef<HTMLDivElement>(null)
  const heroRef = useRef<HTMLElement>(null)
  const heroHeightRef = useRef(800)
  const [menuOpen, setMenuOpen] = useState(false)
  const navigate = useNavigate()

  // Navegação da nav: âncoras (/#id) rolam a home; demais viram rota.
  const go = useCallback((to: string) => {
    setMenuOpen(false)
    if (to.startsWith("/#")) {
      const el = document.getElementById(to.slice(2))
      if (el) { el.scrollIntoView({ behavior: "smooth" }); return }
    }
    navigate(to)
  }, [navigate])

  useEffect(() => {
    const el = heroRef.current
    if (!el) return
    heroHeightRef.current = el.clientHeight
    const ro = new ResizeObserver(([e]) => { heroHeightRef.current = e.contentRect.height })
    ro.observe(el)
    return () => ro.disconnect()
  }, [])

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start start", "end end"],
  })

  // ── Mouse parallax ──────────────────────────────────────────────────────
  const rawMX = useMotionValue(0)
  const rawMY = useMotionValue(0)
  const mx = useSpring(rawMX, { stiffness: 40, damping: 25 })
  const my = useSpring(rawMY, { stiffness: 40, damping: 25 })

  const onMouseMove = (e: React.MouseEvent<HTMLElement>) => {
    if (window.innerWidth <= 767) return
    const r = heroRef.current?.getBoundingClientRect()
    if (!r) return
    rawMX.set((e.clientX - r.left) / r.width - 0.5)
    rawMY.set((e.clientY - r.top) / r.height - 0.5)
  }
  const onMouseLeave = () => { rawMX.set(0); rawMY.set(0) }

  // ── Background & black reveal ───────────────────────────────────────────
  const heroBg    = useTransform(scrollYProgress, [0.41, 0.42], [WHITE, BLACK])
  const blackScale = useTransform(scrollYProgress, [0, 0.42], [0, 17])

  // ── Text colors (timed to when black circle physically reaches the text) ─
  const titleColor   = useTransform(scrollYProgress, [0.05, 0.14], [BLACK, WHITE])
  const descColor    = useTransform(scrollYProgress, [0.05, 0.14], ["rgba(17,17,17,0.58)", "rgba(239,239,239,0.70)"])
  const eyebrowColor = useTransform(scrollYProgress, [0.05, 0.14], ["rgba(17,17,17,0.52)", "rgba(239,239,239,0.62)"])
  const footerColor  = useTransform(scrollYProgress, [0.05, 0.14], ["rgba(17,17,17,0.60)", "rgba(239,239,239,0.72)"])
  const navColor     = useTransform(scrollYProgress, [0.05, 0.14], [BLACK, WHITE])
  const ctaColorFg   = useTransform(scrollYProgress, [0.05, 0.14], [RED, RED])

  // ── Sunset & moonrise — same distance, same speed, opposite directions ──
  const SUNSET_START = 0.30
  const SUNSET_END   = 0.65
  const TRAVEL       = 0.30

  const redSunY         = useTransform(scrollYProgress, (p) =>
    lerp(0, heroHeightRef.current * TRAVEL, (p - SUNSET_START) / (SUNSET_END - SUNSET_START)))
  const redSunOpacity   = useTransform(scrollYProgress, [SUNSET_START + 0.05, SUNSET_END - 0.03], [1, 0])
  const redSunScale     = useTransform(scrollYProgress, [0, 0.28], [1, 1.12])
  const redKanjiOpacity = useTransform(scrollYProgress, [0.10, 0.22], [1, 0])

  const whiteSunY         = useTransform(scrollYProgress, (p) =>
    lerp(heroHeightRef.current * TRAVEL, 0, (p - SUNSET_START) / (SUNSET_END - SUNSET_START)))
  const whiteSunOpacity    = useTransform(scrollYProgress, [SUNSET_START + 0.02, SUNSET_END - 0.05], [0, 1])
  const whiteSunInnerScale = useTransform(scrollYProgress, [SUNSET_START, SUNSET_END], [0.78, 0.92])

  const glowOpacity = useTransform(scrollYProgress, [0.0, 0.18, 0.55, 0.68], [0.9, 1, 0.5, 0])

  // ── Mountain colors ─────────────────────────────────────────────────────
  const hazeColor   = useTransform(scrollYProgress, [0, 0.42], ["#E3E3E3", "#3A3A3A"])
  const farColor    = useTransform(scrollYProgress, [0, 0.42], ["#C7C7C7", "#282828"])
  const middleColor = useTransform(scrollYProgress, [0, 0.42], ["#777777", "#181818"])
  const frontColor  = useTransform(scrollYProgress, [0, 0.42], [BLACK, "#050505"])

  // ── Mountain scroll Y ────────────────────────────────────────────────────
  const hazeYN   = useTransform(scrollYProgress, [0, 0.42, 1], [0, -2,  -4])
  const farYN    = useTransform(scrollYProgress, [0, 0.42, 1], [0, -5,  -8])
  const middleYN = useTransform(scrollYProgress, [0, 0.42, 1], [0, -9, -14])
  const frontYN  = useTransform(scrollYProgress, [0, 0.42, 1], [0, -13, -20])
  const hazeY    = useMotionTemplate`${hazeYN}%`
  const farY     = useMotionTemplate`${farYN}%`
  const middleY  = useMotionTemplate`${middleYN}%`
  const frontY   = useMotionTemplate`${frontYN}%`

  // ── Mouse parallax (matches original GSAP scale) ─────────────────────────
  const hazeMX     = useTransform(mx, (x) => x * 4)
  const farMX      = useTransform(mx, (x) => x * 8)
  const middleMX   = useTransform(mx, (x) => x * 15)
  const frontMX    = useTransform(mx, (x) => x * 25)
  const sunMX      = useTransform(mx, (x) => x * -11)
  const sunMY      = useTransform(my, (y) => y * -7)
  const whiteSunMX = useTransform(mx, (x) => x * -7)

  return (
    <>
      <style>{RESPONSIVE_CSS}</style>

      {/* 400vh scroll zone — hero is sticky inside */}
      <div ref={containerRef} style={{ height: "400vh", position: "relative" }}>
        <motion.section
          ref={heroRef}
          className="sticky top-0 w-full overflow-hidden isolate"
          style={{
            height: "100svh",
            minHeight: "600px",
            backgroundColor: heroBg,
            fontFamily: '"Be Vietnam Pro", sans-serif',
          }}
          onMouseMove={onMouseMove}
          onMouseLeave={onMouseLeave}
        >
          {/* ── Warm sunset glow ── */}
          <motion.div
            aria-hidden="true"
            className="absolute pointer-events-none"
            style={{
              zIndex: 0,
              left: "75%",
              top: "43%",
              width: "90vw",
              aspectRatio: "1/1",
              translateX: "-50%",
              translateY: "-50%",
              y: redSunY,
              opacity: glowOpacity,
              background: "radial-gradient(circle, rgba(242,80,20,0.18) 0%, rgba(255,140,30,0.10) 28%, rgba(255,200,80,0.04) 55%, transparent 72%)",
            }}
          />

          {/* ── Black reveal circle ── */}
          <div
            aria-hidden="true"
            className="hero-sun-anchor absolute pointer-events-none"
            style={{ zIndex: 0, left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)" }}
          >
            <motion.div
              className="w-full h-full rounded-full"
              style={{ backgroundColor: BLACK, scale: blackScale, transformOrigin: "center" }}
            />
          </div>

          {/* ── Scene ── */}
          <div
            aria-hidden="true"
            className="absolute inset-0 overflow-hidden pointer-events-none"
            style={{ zIndex: 1 }}
          >
            {/* Red sun */}
            <div
              className="hero-sun-anchor absolute"
              style={{ left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)", zIndex: 1 }}
            >
              <motion.div style={{ x: sunMX, y: redSunY, width: "100%", height: "100%" }}>
                <motion.div
                  className="w-full h-full rounded-full flex items-center justify-center"
                  style={{ backgroundColor: RED, scale: redSunScale, opacity: redSunOpacity }}
                >
                  <motion.div className="hero-kanji flex items-center justify-center" style={{ opacity: redKanjiOpacity }}>
                    <TabiMark width="62%" color={WHITE} />
                  </motion.div>
                </motion.div>
              </motion.div>
            </div>

            {/* White moon */}
            <div
              className="hero-sun-anchor absolute"
              style={{ left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)", zIndex: 1 }}
            >
              <motion.div style={{ x: whiteSunMX, y: whiteSunY, width: "100%", height: "100%" }}>
                <motion.div
                  className="w-full h-full rounded-full flex items-center justify-center"
                  style={{ backgroundColor: WHITE, scale: whiteSunInnerScale, opacity: whiteSunOpacity }}
                >
                  <TabiMark width="48%" color={RED} opacity={0.75} />
                </motion.div>
              </motion.div>
            </div>

            {/* Mountains */}
            <motion.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible"
              className="hero-mountain hero-mountain-haze absolute"
              style={{ left: "-4%", width: "108%", bottom: "16%", height: "39%", zIndex: 2, color: hazeColor, x: hazeMX, y: hazeY, transformOrigin: "center bottom" }}
            >
              <path d={MOUNTAIN_PATHS.haze} fill="currentColor" />
            </motion.svg>

            <motion.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible"
              className="hero-mountain hero-mountain-far absolute"
              style={{ left: "-4%", width: "108%", bottom: "10%", height: "36%", zIndex: 3, color: farColor, x: farMX, y: farY, transformOrigin: "center bottom" }}
            >
              <path d={MOUNTAIN_PATHS.far} fill="currentColor" />
            </motion.svg>

            <motion.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible"
              className="hero-mountain hero-mountain-middle absolute"
              style={{ left: "-4%", width: "108%", bottom: "4%", height: "31%", zIndex: 5, color: middleColor, x: middleMX, y: middleY, transformOrigin: "center bottom" }}
            >
              <path d={MOUNTAIN_PATHS.middle} fill="currentColor" />
            </motion.svg>

            <motion.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible"
              className="hero-mountain hero-mountain-front absolute"
              style={{ left: "-4%", width: "108%", bottom: "-2px", height: "23%", zIndex: 6, color: frontColor, x: frontMX, y: frontY, transformOrigin: "center bottom" }}
            >
              <path d={MOUNTAIN_PATHS.front} fill="currentColor" />
            </motion.svg>
          </div>

          {/* ── Nav ── */}
          <motion.nav
            className="absolute top-0 left-0 w-full flex items-center justify-between pointer-events-auto"
            style={{ zIndex: 30, padding: "clamp(18px, 3vw, 40px) clamp(20px, 4vw, 82px)", color: navColor }}
            initial={{ opacity: 0, y: -8 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, ease: "easeOut" }}
          >
            <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(13px, 1.2vw, 19px)", letterSpacing: "-0.04em", textTransform: "uppercase" }}>
              STUDIO TABI
            </span>
            {/* Desktop links */}
            <div className="hidden md:flex items-center gap-8">
              {NAV_LINKS.map((item) => (
                <span key={item.label} onClick={() => go(item.to)} className="cursor-pointer opacity-50 hover:opacity-100 transition-opacity duration-200"
                  style={{ fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em" }}>
                  {item.label}
                </span>
              ))}
            </div>
            {/* Mobile: hamburger button */}
            <button
              className="flex md:hidden flex-col justify-center items-end gap-[5px] cursor-pointer bg-transparent border-none p-2 -mr-2"
              style={{ color: "currentColor" }}
              onClick={() => setMenuOpen(o => !o)}
              aria-label="Menu"
            >
              <motion.span
                className="block h-px bg-current"
                animate={{ width: menuOpen ? 20 : 20, rotate: menuOpen ? 45 : 0, y: menuOpen ? 6 : 0 }}
                transition={{ duration: 0.28, ease: [0.16, 1, 0.3, 1] }}
              />
              <motion.span
                className="block h-px bg-current"
                animate={{ width: menuOpen ? 20 : 12, rotate: menuOpen ? -45 : 0, y: menuOpen ? -1 : 0 }}
                transition={{ duration: 0.28, ease: [0.16, 1, 0.3, 1] }}
              />
            </button>
          </motion.nav>

          {/* ── Mobile drawer ── */}
          <motion.div
            className="fixed inset-0 md:hidden pointer-events-none"
            style={{ zIndex: 25 }}
            animate={{ opacity: menuOpen ? 1 : 0 }}
            transition={{ duration: 0.22 }}
          >
            {/* Backdrop */}
            <div
              className="absolute inset-0"
              style={{ background: "rgba(0,0,0,0.55)", backdropFilter: "blur(4px)", pointerEvents: menuOpen ? "auto" : "none" }}
              onClick={() => setMenuOpen(false)}
            />
            {/* Panel */}
            <motion.div
              className="absolute top-0 right-0 h-full flex flex-col"
              style={{
                width: "min(300px, 85vw)",
                background: "#111111",
                pointerEvents: menuOpen ? "auto" : "none",
                paddingTop: "clamp(72px, 12svh, 100px)",
                paddingBottom: 40,
                paddingLeft: 32,
                paddingRight: 32,
              }}
              initial={{ x: "100%" }}
              animate={{ x: menuOpen ? "0%" : "100%" }}
              transition={{ duration: 0.38, ease: [0.16, 1, 0.3, 1] }}
            >
              {/* Nav items */}
              <nav className="flex flex-col gap-1 flex-1">
                {NAV_LINKS.map((item, i) => (
                  <motion.button
                    key={item.label}
                    className="text-left bg-transparent border-none cursor-pointer group flex items-center gap-3 py-4 border-b"
                    style={{
                      fontFamily: '"Roboto Condensed", sans-serif',
                      fontWeight: 900,
                      fontSize: "clamp(22px, 6vw, 30px)",
                      letterSpacing: "-0.04em",
                      textTransform: "uppercase",
                      color: WHITE,
                      borderColor: "rgba(239,239,239,0.08)",
                    }}
                    initial={{ x: 24, opacity: 0 }}
                    animate={{ x: menuOpen ? 0 : 24, opacity: menuOpen ? 1 : 0 }}
                    transition={{ duration: 0.4, delay: menuOpen ? 0.12 + i * 0.06 : 0, ease: [0.16, 1, 0.3, 1] }}
                    onClick={() => go(item.to)}
                    whileHover={{ x: 6 } as any}
                  >
                    <span style={{ fontSize: 8, color: RED, fontFamily: '"Be Vietnam Pro", sans-serif', fontWeight: 600, letterSpacing: "0.1em", opacity: 0.7 }}>
                      0{i + 1}
                    </span>
                    {item.label}
                  </motion.button>
                ))}
              </nav>
              {/* Bottom CTA */}
              <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: menuOpen ? 1 : 0, y: menuOpen ? 0 : 12 }}
                transition={{ duration: 0.4, delay: menuOpen ? 0.38 : 0 }}
              >
                <p style={{ fontSize: 10, letterSpacing: "0.12em", color: "rgba(239,239,239,0.35)", marginBottom: 16, fontFamily: '"Be Vietnam Pro", sans-serif', fontWeight: 600 }}>
                  INICIAR PROJETO
                </p>
                <a href="mailto:oi@studiotabi.com.br"
                  style={{ fontSize: 13, color: RED, fontFamily: '"Be Vietnam Pro", sans-serif', fontWeight: 500, textDecoration: "none" }}>
                  oi@studiotabi.com.br
                </a>
              </motion.div>
            </motion.div>
          </motion.div>

          {/* ── Content ── */}
          <div
            className="hero-content absolute top-0 left-0 bottom-0 flex flex-col justify-center items-start pointer-events-none"
            style={{ zIndex: 10, width: "min(46%, 780px)", padding: "55px 0 155px clamp(20px, 4vw, 82px)" }}
          >
            <motion.div
              className="flex items-center gap-3 mb-7"
              style={{ color: eyebrowColor, fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", lineHeight: 1 }}
              initial={{ y: 14, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ duration: 0.75, delay: 0.55, ease: "easeOut" }}
            >
              <span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} />
              <span>STUDIO TABI — DIGITAL STUDIO</span>
            </motion.div>

            <h1
              className="hero-title m-0"
              style={{ fontFamily: '"Roboto Condensed", sans-serif', fontSize: "clamp(52px, 5.2vw, 100px)", fontWeight: 900, lineHeight: 0.79, letterSpacing: "-0.05em", textTransform: "uppercase", maxWidth: 760 }}
            >
              {content.hero.titleLines.map((line, i) => (
                <motion.span
                  key={line}
                  className="hero-title-line block"
                  style={{ color: titleColor }}
                  initial={{ y: "108%", opacity: 0 }}
                  animate={{ y: "0%", opacity: 1 }}
                  transition={{ duration: 1, delay: 0.25 + i * 0.07, ease: EASE_OUT_EXPO }}
                >
                  {line}
                </motion.span>
              ))}
              <motion.span
                className="hero-title-line block"
                style={{ color: titleColor, whiteSpace: "nowrap" }}
                initial={{ y: "108%", opacity: 0 }}
                animate={{ y: "0%", opacity: 1 }}
                transition={{ duration: 1, delay: 0.46, ease: EASE_OUT_EXPO }}
              >
                <strong style={{ font: "inherit", color: RED }}>DIGITAL.</strong>
              </motion.span>
            </h1>

            <motion.p
              className="hero-desc"
              style={{ fontSize: "clamp(12px, 0.9vw, 16px)", fontWeight: 400, lineHeight: 1.65, color: descColor, width: "min(88%, 500px)", marginTop: 34, marginBottom: 0 }}
              initial={{ y: 16, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ duration: 0.85, delay: 0.65, ease: "easeOut" }}
            >
              {content.hero.description}
            </motion.p>

            <motion.div
              className="flex flex-wrap items-center gap-4 mt-9 pointer-events-auto"
              initial={{ y: 14, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ duration: 0.85, delay: 0.80, ease: "easeOut" }}
            >
              <motion.button
                className="group flex items-center gap-3 rounded-full border font-semibold"
                style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", letterSpacing: "0.13em", padding: "13px 26px", borderColor: RED, color: RED, backgroundColor: "rgba(0,0,0,0)", cursor: "pointer" }}
                whileHover={{ backgroundColor: RED, color: WHITE }}
                transition={{ duration: 0.22 }}
              >
                VER PORTFÓLIO
                <span className="inline-block transition-transform duration-300 group-hover:translate-x-0.5" style={{ fontSize: 13 }}>→</span>
              </motion.button>

              <motion.button
                className="hero-cta-secondary"
                style={{ fontSize: "9.5px", letterSpacing: "0.13em", color: ctaColorFg, backgroundColor: "transparent", border: "none", cursor: "pointer", padding: 0, fontFamily: '"Be Vietnam Pro", sans-serif', fontWeight: 500, opacity: 0.5 }}
                whileHover={{ opacity: 1 }}
                transition={{ duration: 0.2 }}
              >
                FALAR COM A EQUIPE
              </motion.button>
            </motion.div>
          </div>

          {/* ── Footer ── */}
          <motion.div
            className="hero-footer absolute left-0 bottom-0 w-full flex items-center justify-between"
            style={{ zIndex: 12, padding: "0 clamp(20px, 4vw, 82px) 32px", fontSize: 10, fontWeight: 500, letterSpacing: "0.08em", color: footerColor }}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.8, delay: 0.78 }}
          >
            <div className="flex items-center gap-2">
              <span>SCROLL</span>
              <motion.span
                style={{ color: RED, fontSize: 15 }}
                animate={{ y: [0, 4, 0] }}
                transition={{ repeat: Infinity, duration: 2.2, ease: "easeInOut" }}
              >
                ↘
              </motion.span>
            </div>
            <div className="flex items-center gap-4 md:gap-6">
              <span className="hidden sm:inline" style={{ opacity: 0.45 }}>PT / EN</span>
              <span>©2026</span>
            </div>
          </motion.div>

        </motion.section>
      </div>

      <AboutSection />
      <ServicesSection />
      <ProjectsSection />
      <FaqSection />
      <SiteFooter />
    </>
  )
}

// ── Reveal ──────────────────────────────────────────────────────────────────
function Reveal({ children, delay = 0, className = "" }: { children: React.ReactNode; delay?: number; className?: string }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-80px" })
  return (
    <motion.div ref={ref} className={className}
      initial={{ opacity: 0, y: 24 }} animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.85, delay, ease: [0.16, 1, 0.3, 1] }}>
      {children}
    </motion.div>
  )
}

// ── Headline line (clips up from below) ─────────────────────────────────────
function HeadlineLine({ children, delay, color = WHITE }: { children: React.ReactNode; delay: number; color?: string }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-60px" })
  return (
    <div ref={ref} style={{ overflow: "hidden", lineHeight: 0.88 }}>
      <motion.span className="block" style={{ color }}
        initial={{ y: "110%" }} animate={inView ? { y: "0%" } : {}}
        transition={{ duration: 1, delay, ease: [0.16, 1, 0.3, 1] }}>
        {children}
      </motion.span>
    </div>
  )
}

// ── Animated counter stat ────────────────────────────────────────────────────
function Stat({ numeric, suffix, label, delay }: { numeric: number; suffix: string; label: string; delay: number }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-60px" })
  const mv = useMotionValue(0)
  const rounded = useTransform(mv, (v) => Math.round(v))

  useEffect(() => {
    if (!inView) return
    const timeout = setTimeout(() => {
      const start = performance.now()
      const duration = 1400
      function tick(now: number) {
        const t = Math.min((now - start) / duration, 1)
        mv.set(numeric * (1 - Math.pow(1 - t, 3)))
        if (t < 1) requestAnimationFrame(tick)
      }
      requestAnimationFrame(tick)
    }, delay * 1000)
    return () => clearTimeout(timeout)
  }, [inView, numeric, delay, mv])

  return (
    <motion.div ref={ref} className="flex flex-col gap-3"
      initial={{ opacity: 0, y: 20 }} animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.7, delay, ease: [0.16, 1, 0.3, 1] }}>
      <div style={{ position: "relative", height: "1px", backgroundColor: "rgba(239,239,239,0.08)", marginBottom: 20 }}>
        <motion.div style={{ position: "absolute", inset: 0, backgroundColor: RED, transformOrigin: "left" }}
          initial={{ scaleX: 0 }} animate={inView ? { scaleX: 1 } : {}}
          transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }} />
      </div>
      <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(36px, 4.5vw, 72px)", lineHeight: 0.85, letterSpacing: "-0.04em", color: WHITE, display: "flex", alignItems: "baseline", gap: "2px" }}>
        <motion.span>{rounded}</motion.span>
        <span>{suffix}</span>
      </span>
      <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "10px", fontWeight: 600, letterSpacing: "0.14em", color: "rgba(239,239,239,0.40)" }}>
        {label}
      </span>
    </motion.div>
  )
}

// ── Pillar ───────────────────────────────────────────────────────────────────
function Pillar({ index, title, body, delay }: { index: string; title: string; body: string; delay: number }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-50px" })
  return (
    <div ref={ref} style={{ position: "relative" }}>
      <div style={{ position: "relative", height: "1px", backgroundColor: "rgba(239,239,239,0.08)" }}>
        <motion.div style={{ position: "absolute", inset: 0, backgroundColor: "rgba(239,239,239,0.22)", transformOrigin: "left" }}
          initial={{ scaleX: 0 }} animate={inView ? { scaleX: 1 } : {}}
          transition={{ duration: 0.7, delay, ease: [0.16, 1, 0.3, 1] }} />
      </div>
      <motion.div className="flex items-start gap-4 md:gap-5 py-6 md:py-8"
        initial={{ opacity: 0 }} animate={inView ? { opacity: 1 } : {}}
        transition={{ duration: 0.5, delay: delay + 0.15 }}>
        <motion.span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.14em", color: RED, paddingTop: "3px", flexShrink: 0 }}
          initial={{ opacity: 0 }} animate={inView ? { opacity: 1 } : {}}
          transition={{ duration: 0.4, delay: delay + 0.2 }}>
          {index}
        </motion.span>
        <div className="flex flex-col gap-2 md:gap-3 flex-1">
          <div style={{ overflow: "hidden" }}>
            <motion.span className="block"
              style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(16px, 1.6vw, 26px)", letterSpacing: "-0.03em", textTransform: "uppercase", color: WHITE, lineHeight: 1 }}
              initial={{ y: "105%" }} animate={inView ? { y: "0%" } : {}}
              transition={{ duration: 0.7, delay: delay + 0.18, ease: [0.16, 1, 0.3, 1] }}>
              {title}
            </motion.span>
          </div>
          <motion.span
            style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(11px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.65, color: "rgba(239,239,239,0.48)" }}
            initial={{ opacity: 0, y: 8 }} animate={inView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.7, delay: delay + 0.30, ease: "easeOut" }}>
            {body}
          </motion.span>
        </div>
        <motion.span style={{ color: "rgba(239,239,239,0.18)", fontSize: 13, flexShrink: 0, paddingTop: 3 }}
          className="hidden sm:block"
          initial={{ opacity: 0, x: -6 }} animate={inView ? { opacity: 1, x: 0 } : {}}
          transition={{ duration: 0.5, delay: delay + 0.38 }}>
          →
        </motion.span>
      </motion.div>
    </div>
  )
}

// ── About section ────────────────────────────────────────────────────────────
function AboutSection() {
  const { content } = useContent()
  const sectionRef = useRef<HTMLElement>(null)
  const { scrollYProgress: sectionScroll } = useScroll({ target: sectionRef, offset: ["start end", "end start"] })
  const kanjiY      = useTransform(sectionScroll, [0, 1], ["8%", "-10%"])
  const kanjiOpacity = useTransform(sectionScroll, [0, 0.15, 0.7, 1], [0, 0.055, 0.055, 0])
  const kanjiRotate  = useTransform(sectionScroll, [0, 1], [-2, 2])

  const STATS = content.about.stats
  const PILLARS = content.about.pillars


  const pad = "clamp(20px, 4vw, 82px)"

  return (
    <section id="sobre" ref={sectionRef} style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative", overflow: "hidden" }}>

      {/* Brand mark background */}
      <motion.div aria-hidden="true" className="absolute pointer-events-none"
        style={{ opacity: kanjiOpacity, right: "-5%", top: "6%", y: kanjiY, rotate: kanjiRotate, width: "clamp(260px, 38vw, 680px)" }}>
        <TabiMark width="100%" color={WHITE} />
      </motion.div>

      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.08)" }} />

      {/* ── Upper: eyebrow + headline + text ── */}
      <div style={{ padding: `clamp(56px, 9vw, 130px) ${pad} 0`, position: "relative", zIndex: 1 }}>

        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12"
            style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <motion.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }}
              initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }}
              transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }} />
            <span>STUDIO TABI — SOBRE NÓS</span>
          </div>
        </Reveal>

        {/* Responsive grid — Tailwind classes only, no inline gridTemplateColumns */}
        <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(32px, 6vw, 100px)", alignItems: "start" }}>

          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(42px, 5.4vw, 92px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, display: "flex", flexDirection: "column", gap: "0.06em" }}>
            <HeadlineLine delay={0.05}>NÃO FAZEMOS</HeadlineLine>
            <HeadlineLine delay={0.12}>SITES.</HeadlineLine>
            <HeadlineLine delay={0.19} color={RED}>CONSTRUÍMOS</HeadlineLine>
            <HeadlineLine delay={0.26}>PRESENÇA.</HeadlineLine>
          </h2>

          <div className="flex flex-col" style={{ gap: "clamp(24px, 4vw, 56px)" }}>
            <Reveal delay={0.12}>
              <p style={{ fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}>
                {content.about.paragraph1}
              </p>
            </Reveal>
            <Reveal delay={0.18}>
              <p style={{ fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}>
                {content.about.paragraph2}
              </p>
            </Reveal>
            <Reveal delay={0.24}>
              <motion.button className="flex items-center gap-3 self-start"
                style={{ fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED, backgroundColor: "transparent", border: "none", cursor: "pointer", padding: 0, fontFamily: '"Be Vietnam Pro", sans-serif' }}
                whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }}>
                CONHEÇA NOSSA HISTÓRIA
                <span style={{ fontSize: 13 }}>→</span>
              </motion.button>
            </Reveal>
          </div>
        </div>
      </div>

      {/* ── Stats ── */}
      <div
        className="grid grid-cols-2 md:grid-cols-4"
        style={{ padding: `clamp(48px, 8vw, 110px) ${pad}`, gap: "clamp(20px, 3vw, 48px)", position: "relative", zIndex: 1 }}
      >
        {STATS.map((s, i) => (
          <Stat key={s.label} numeric={s.numeric} suffix={s.suffix} label={s.label} delay={i * 0.12} />
        ))}
      </div>

      <div style={{ margin: `0 ${pad}`, height: "1px", backgroundColor: "rgba(239,239,239,0.08)", position: "relative", zIndex: 1 }} />

      {/* ── Pillars ── */}
      <div style={{ padding: `0 ${pad} clamp(64px, 10vw, 140px)`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center justify-between pt-10 md:pt-12 pb-2">
            <span style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.30)" }}>
              COMO TRABALHAMOS
            </span>
            <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.20)" }}>
              04 PILARES
            </span>
          </div>
        </Reveal>

        {PILLARS.map((p, i) => (
          <Pillar key={p.title} index={`0${i + 1}`} title={p.title} body={p.body} delay={i * 0.06} />
        ))}
      </div>

    </section>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// SERVICES SECTION
// ─────────────────────────────────────────────────────────────────────────────
function ServiceCard({ num, title, body, delay }: { num: string; title: string; body: string; delay: number }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-60px" })
  return (
    <motion.div
      ref={ref}
      style={{ borderTop: "1px solid rgba(239,239,239,0.10)", paddingTop: "clamp(24px,3vw,36px)", paddingBottom: "clamp(24px,3vw,36px)", position: "relative", overflow: "hidden" }}
      initial={{ opacity: 0, y: 30 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.8, delay, ease: [0.16, 1, 0.3, 1] }}
    >
      {/* Red line wipe on top border */}
      <motion.div
        style={{ position: "absolute", top: 0, left: 0, right: 0, height: "1px", backgroundColor: RED, transformOrigin: "left" }}
        initial={{ scaleX: 0 }}
        animate={inView ? { scaleX: 1 } : {}}
        transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }}
      />
      <span style={{ display: "block", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.16em", color: RED, marginBottom: 16 }}>{num}</span>
      <div style={{ overflow: "hidden", marginBottom: 14 }}>
        <motion.h3
          style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 1.7vw, 28px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: WHITE, lineHeight: 0.95, margin: 0 }}
          initial={{ y: "105%" }}
          animate={inView ? { y: "0%" } : {}}
          transition={{ duration: 0.75, delay: delay + 0.12, ease: [0.16, 1, 0.3, 1] }}
        >
          {title}
        </motion.h3>
      </div>
      <motion.p
        style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.65, color: "rgba(239,239,239,0.45)", margin: 0 }}
        initial={{ opacity: 0 }}
        animate={inView ? { opacity: 1 } : {}}
        transition={{ duration: 0.7, delay: delay + 0.22 }}
      >
        {body}
      </motion.p>
      <motion.span
        style={{ display: "block", marginTop: 20, fontSize: 11, color: "rgba(239,239,239,0.20)" }}
        initial={{ opacity: 0, x: -6 }}
        animate={inView ? { opacity: 1, x: 0 } : {}}
        transition={{ duration: 0.5, delay: delay + 0.30 }}
      >
        →
      </motion.span>
    </motion.div>
  )
}

function ServicesSection() {
  const { content } = useContent()
  const pad = "clamp(20px, 4vw, 82px)"
  return (
    <section style={{ backgroundColor: "#0D0D0D", fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative", overflow: "hidden" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }} />

      {/* Decorative large number */}
      <div aria-hidden="true" style={{ position: "absolute", right: "-2%", top: "4%", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(120px, 22vw, 400px)", lineHeight: 1, letterSpacing: "-0.08em", color: WHITE, opacity: 0.025, userSelect: "none", pointerEvents: "none" }}>
        06
      </div>

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad} 0`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <motion.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }} />
            <span>STUDIO TABI — SERVIÇOS</span>
          </div>
        </Reveal>

        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 md:gap-0" style={{ marginBottom: "clamp(40px, 6vw, 80px)" }}>
          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(40px, 5.4vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.85 }}>
            <HeadlineLine delay={0.05}>O QUE</HeadlineLine>
            <HeadlineLine delay={0.12}>ENTREGAMOS.</HeadlineLine>
          </h2>
          <Reveal delay={0.18}>
            <motion.button
              style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10, whiteSpace: "nowrap", paddingBottom: 8 }}
              whileHover={{ gap: "18px" } as any}
              transition={{ duration: 0.22 }}
            >
              VER TODOS OS SERVIÇOS <span style={{ fontSize: 13 }}>→</span>
            </motion.button>
          </Reveal>
        </div>
      </div>

      <div
        className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"
        style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "0 clamp(24px, 3vw, 48px)", position: "relative", zIndex: 1 }}
      >
        {content.services.map((s, i) => (
          <ServiceCard key={s.num} num={s.num} title={s.title} body={s.body} delay={i * 0.08} />
        ))}
      </div>
    </section>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// PROJECTS SECTION
// ─────────────────────────────────────────────────────────────────────────────
type Project = SiteContent["projects"][0]

// ─────────────────────────────────────────────────────────────────────────────
// PROJECT DETAIL PAGE
// ─────────────────────────────────────────────────────────────────────────────

function ProjectPageBody({ proj, prevSlug, nextSlug }: {
  proj: Project
  prevSlug: string
  nextSlug: string
}) {
  const detail = proj.detail
  const gallery = proj.gallery ?? []
  const pad = "clamp(20px, 5vw, 90px)"

  const stagger = (i: number) => ({ initial: { opacity: 0, y: 28 }, animate: { opacity: 1, y: 0 }, transition: { duration: 0.75, delay: 0.18 + i * 0.08, ease: EASE_OUT_EXPO } })

  return (
    <motion.div
      style={{ backgroundColor: "#080808", fontFamily: '"Be Vietnam Pro", sans-serif' }}
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ duration: 0.35 }}
    >
      {/* ── Hero ── */}
      <div style={{ position: "relative", height: "clamp(380px, 55vh, 620px)", overflow: "hidden" }}>
        {/* Background */}
        <div style={{ position: "absolute", inset: 0, background: proj.bg }} />
        <div style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 70% 50%, ${proj.accent}30 0%, transparent 65%)` }} />
        {/* Grid lines */}
        <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px)`, backgroundSize: "60px 60px" }} />
        {/* Large decorative project number */}
        <div aria-hidden="true" style={{ position: "absolute", right: "-2%", bottom: "-8%", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(160px, 22vw, 340px)", lineHeight: 0.85, letterSpacing: "-0.08em", color: WHITE, opacity: 0.05, userSelect: "none" }}>
          {proj.id}
        </div>
        {/* Mock interface */}
        <div style={{ position: "absolute", right: pad, top: "50%", transform: "translateY(-50%)", width: "clamp(160px, 28vw, 360px)", background: "rgba(255,255,255,0.04)", border: `1px solid ${proj.accent}33`, borderRadius: 8, backdropFilter: "blur(12px)", overflow: "hidden" }}>
          <div style={{ padding: "12px 16px", borderBottom: `1px solid ${proj.accent}22`, display: "flex", alignItems: "center", gap: 8 }}>
            <span style={{ width: 7, height: 7, borderRadius: "50%", backgroundColor: proj.accent, display: "inline-block" }} />
            <span style={{ fontSize: 9, letterSpacing: "0.14em", color: "rgba(239,239,239,0.5)", fontWeight: 600 }}>{proj.name.toUpperCase()}</span>
          </div>
          {detail.mockupLines.map((line, i) => (
            <div key={i} style={{ padding: "10px 16px", borderBottom: i < detail.mockupLines.length - 1 ? `1px solid rgba(255,255,255,0.04)` : "none", display: "flex", alignItems: "center", justifyContent: "space-between" }}>
              <span style={{ fontSize: 11, color: i === 0 ? WHITE : "rgba(239,239,239,0.35)", fontWeight: i === 0 ? 600 : 400, letterSpacing: "0.06em" }}>{line}</span>
              <span style={{ fontSize: 8, color: proj.accent, fontWeight: 600 }}>{i === 0 ? "ATIVO" : "—"}</span>
            </div>
          ))}
        </div>
        {/* Back button */}
        <Link
          to="/#trabalhos"
          style={{ position: "absolute", top: "clamp(18px, 3vw, 32px)", left: pad, background: "rgba(0,0,0,0.45)", border: "1px solid rgba(239,239,239,0.14)", borderRadius: 999, color: WHITE, fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", padding: "10px 18px", cursor: "pointer", display: "flex", alignItems: "center", gap: 8, fontFamily: '"Be Vietnam Pro", sans-serif', backdropFilter: "blur(8px)", textDecoration: "none" }}
        >
          ← VOLTAR
        </Link>
        {/* Title */}
        <div style={{ position: "absolute", bottom: "clamp(32px, 5vw, 56px)", left: pad }}>
          <motion.p {...stagger(0)} style={{ margin: "0 0 8px", fontSize: 9, fontWeight: 600, letterSpacing: "0.16em", color: `${proj.accent}` }}>
            {proj.category.toUpperCase()} — {proj.year}
          </motion.p>
          <motion.h1
            {...stagger(1)}
            style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(36px, 5.5vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", color: WHITE, margin: 0, lineHeight: 0.85 }}
          >
            {proj.name}
          </motion.h1>
        </div>
        {/* Bottom gradient */}
        <div style={{ position: "absolute", bottom: 0, left: 0, right: 0, height: "40%", background: "linear-gradient(to top, #080808 0%, transparent 100%)" }} />
      </div>

      {/* ── Body ── */}
      <div style={{ maxWidth: 1200, margin: "0 auto", padding: `clamp(40px, 6vw, 80px) ${pad}` }}>

        {/* Metadata row */}
        <motion.div
          {...stagger(2)}
          className="grid grid-cols-2 md:grid-cols-4"
          style={{ gap: "1px", backgroundColor: "rgba(239,239,239,0.08)", border: "1px solid rgba(239,239,239,0.08)", borderRadius: 6, overflow: "hidden", marginBottom: "clamp(48px, 7vw, 88px)" }}
        >
          {[
            { label: "Cliente", value: detail.client },
            { label: "Duração", value: detail.duration },
            { label: "Serviços", value: detail.scope.length + " áreas" },
            { label: "Ano", value: proj.year },
          ].map((item) => (
            <div key={item.label} style={{ padding: "clamp(18px,2.5vw,28px)", backgroundColor: "#0D0D0D" }}>
              <p style={{ margin: "0 0 6px", fontSize: 8, fontWeight: 600, letterSpacing: "0.16em", color: "rgba(239,239,239,0.30)" }}>{item.label.toUpperCase()}</p>
              <p style={{ margin: 0, fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: "clamp(14px,1.4vw,20px)", letterSpacing: "-0.03em", color: WHITE }}>{item.value}</p>
            </div>
          ))}
        </motion.div>

        {/* Scope tags */}
        <motion.div {...stagger(3)} style={{ display: "flex", flexWrap: "wrap", gap: 8, marginBottom: "clamp(56px, 8vw, 100px)" }}>
          {detail.scope.map((tag) => (
            <span key={tag} style={{ fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", color: proj.accent, border: `1px solid ${proj.accent}44`, borderRadius: 999, padding: "7px 14px" }}>
              {tag.toUpperCase()}
            </span>
          ))}
        </motion.div>

        {/* Two columns: Challenge + Solution */}
        <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(32px, 5vw, 72px)", marginBottom: "clamp(56px, 8vw, 100px)" }}>
          <motion.div {...stagger(4)}>
            <p style={{ margin: "0 0 20px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>O DESAFIO</p>
            <div style={{ width: 32, height: 2, backgroundColor: proj.accent, marginBottom: 24 }} />
            <p style={{ fontSize: "clamp(14px, 1.1vw, 17px)", fontWeight: 400, lineHeight: 1.78, color: "rgba(239,239,239,0.65)", margin: 0 }}>
              {detail.challenge}
            </p>
          </motion.div>
          <motion.div {...stagger(5)}>
            <p style={{ margin: "0 0 20px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>A SOLUÇÃO</p>
            <div style={{ width: 32, height: 2, backgroundColor: "rgba(239,239,239,0.25)", marginBottom: 24 }} />
            <p style={{ fontSize: "clamp(14px, 1.1vw, 17px)", fontWeight: 400, lineHeight: 1.78, color: "rgba(239,239,239,0.65)", margin: 0 }}>
              {detail.solution}
            </p>
          </motion.div>
        </div>

        {/* Results */}
        <motion.div {...stagger(6)} style={{ marginBottom: "clamp(56px, 8vw, 100px)" }}>
          <p style={{ margin: "0 0 32px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>RESULTADOS</p>
          <div className="grid grid-cols-2 md:grid-cols-4" style={{ gap: "clamp(16px, 2vw, 24px)" }}>
            {detail.results.map((r, i) => (
              <motion.div
                key={r.label}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.65, delay: i * 0.08, ease: EASE_OUT_EXPO }}
                style={{ padding: "clamp(20px,2.5vw,32px)", backgroundColor: "#0D0D0D", border: "1px solid rgba(239,239,239,0.07)", borderRadius: 6 }}
              >
                <p style={{ margin: "0 0 8px", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(28px, 3.5vw, 48px)", letterSpacing: "-0.06em", color: proj.accent, lineHeight: 1 }}>
                  {r.value}
                </p>
                <p style={{ margin: 0, fontSize: 10, fontWeight: 500, color: "rgba(239,239,239,0.40)", lineHeight: 1.4 }}>
                  {r.label}
                </p>
              </motion.div>
            ))}
          </div>
        </motion.div>

        {/* Visual full-width strip */}
        <motion.div
          initial={{ opacity: 0, scaleX: 0.96 }}
          whileInView={{ opacity: 1, scaleX: 1 }}
          viewport={{ once: true }}
          transition={{ duration: 0.9, ease: EASE_OUT_EXPO }}
          style={{ height: "clamp(180px, 28vw, 340px)", borderRadius: 8, overflow: "hidden", position: "relative", marginBottom: "clamp(56px, 8vw, 100px)" }}
        >
          <div style={{ position: "absolute", inset: 0, background: proj.bg }} />
          <div style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 30% 50%, ${proj.accent}28 0%, transparent 60%)` }} />
          <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px)`, backgroundSize: "50px 50px" }} />
          <div style={{ position: "absolute", inset: 0, display: "flex", alignItems: "center", justifyContent: "center" }}>
            <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(48px, 8vw, 120px)", letterSpacing: "-0.06em", color: WHITE, opacity: 0.08, userSelect: "none", textTransform: "uppercase" }}>
              {proj.name}
            </span>
          </div>
        </motion.div>

        {/* Gallery */}
        {gallery.length > 0 && (
          <motion.div {...stagger(7)} style={{ marginBottom: "clamp(56px, 8vw, 100px)" }}>
            <p style={{ margin: "0 0 32px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>GALERIA</p>
            <div className="grid grid-cols-1 sm:grid-cols-2" style={{ gap: "clamp(12px, 1.6vw, 20px)" }}>
              {gallery.map((url, i) => (
                <motion.div
                  key={url + i}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.7, delay: (i % 2) * 0.08, ease: EASE_OUT_EXPO }}
                  style={{ borderRadius: 8, overflow: "hidden", border: "1px solid rgba(239,239,239,0.08)", aspectRatio: "4/3", background: "#0D0D0D" }}
                >
                  <img src={url} alt={`${proj.name} — imagem ${i + 1}`} loading="lazy" style={{ width: "100%", height: "100%", objectFit: "cover", display: "block" }} />
                </motion.div>
              ))}
            </div>
          </motion.div>
        )}

        {/* Navigation between projects */}
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", paddingTop: "clamp(24px,3vw,40px)", borderTop: "1px solid rgba(239,239,239,0.08)" }}>
          <Link
            to={`/projeto/${prevSlug}`}
            style={{ textDecoration: "none", color: "rgba(239,239,239,0.40)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", display: "flex", alignItems: "center", gap: 10 }}
          >
            ← PROJETO ANTERIOR
          </Link>
          <Link
            to="/#trabalhos"
            style={{ textDecoration: "none", color: "rgba(239,239,239,0.25)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em" }}
          >
            TODOS OS PROJETOS
          </Link>
          <Link
            to={`/projeto/${nextSlug}`}
            style={{ textDecoration: "none", color: "rgba(239,239,239,0.40)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", display: "flex", alignItems: "center", gap: 10 }}
          >
            PRÓXIMO PROJETO →
          </Link>
        </div>
      </div>
    </motion.div>
  )
}

function ProjectCard({ proj, index, onClick }: { proj: Project; index: number; onClick: () => void }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-80px" })
  const [hovered, setHovered] = useState(false)

  return (
    <motion.div
      ref={ref}
      style={{ position: "relative", overflow: "hidden", borderRadius: 4, cursor: "pointer", aspectRatio: proj.featured ? "4/3" : "1/1" }}
      initial={{ opacity: 0, y: 40 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.9, delay: index * 0.1, ease: [0.16, 1, 0.3, 1] }}
      onHoverStart={() => setHovered(true)}
      onHoverEnd={() => setHovered(false)}
      onClick={onClick}
    >
      {/* Background */}
      {proj.imageUrl ? (
        <>
          <div style={{ position: "absolute", inset: 0, backgroundImage: `url(${proj.imageUrl})`, backgroundSize: "cover", backgroundPosition: "center" }} />
          <div style={{ position: "absolute", inset: 0, background: "rgba(0,0,0,0.45)" }} />
        </>
      ) : (
        <div style={{ position: "absolute", inset: 0, background: proj.bg }} />
      )}

      {/* Accent glow */}
      <motion.div
        style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 60% 50%, ${proj.accent}22 0%, transparent 65%)` }}
        animate={{ opacity: hovered ? 1.4 : 0.7 }}
        transition={{ duration: 0.4 }}
      />

      {/* Grid lines decoration */}
      {!proj.imageUrl && <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px)`, backgroundSize: "40px 40px" }} />}

      {/* Project number */}
      <div style={{ position: "absolute", top: "clamp(20px,3vw,32px)", right: "clamp(20px,3vw,32px)", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(48px,6vw,90px)", lineHeight: 1, letterSpacing: "-0.08em", color: WHITE, opacity: 0.06, userSelect: "none" }}>
        {proj.id}
      </div>

      {/* Accent dot */}
      <motion.div
        style={{ position: "absolute", top: "clamp(20px,3vw,32px)", left: "clamp(20px,3vw,32px)", width: 8, height: 8, borderRadius: "50%", backgroundColor: proj.accent }}
        animate={{ scale: hovered ? 1.4 : 1 }}
        transition={{ duration: 0.3 }}
      />

      {/* Content at bottom */}
      <div style={{ position: "absolute", bottom: 0, left: 0, right: 0, padding: "clamp(20px,3vw,32px)", background: "linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%)" }}>
        <div style={{ overflow: "hidden" }}>
          <motion.h3
            style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 2vw, 32px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: WHITE, margin: 0, lineHeight: 0.9 }}
            animate={{ y: hovered ? -4 : 0 }}
            transition={{ duration: 0.3 }}
          >
            {proj.name}
          </motion.h3>
        </div>
        <div className="flex items-center justify-between mt-2">
          <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.14em", color: "rgba(239,239,239,0.50)" }}>
            {proj.category}
          </span>
          <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.30)" }}>
            {proj.year}
          </span>
        </div>
      </div>

      {/* Hover overlay */}
      <motion.div
        style={{ position: "absolute", inset: 0, border: `1px solid ${proj.accent}`, borderRadius: 4, pointerEvents: "none" }}
        animate={{ opacity: hovered ? 0.5 : 0 }}
        transition={{ duration: 0.3 }}
      />
    </motion.div>
  )
}

function ProjectsSection() {
  const { content } = useContent()
  const navigate = useNavigate()
  const projects = content.projects
  const pad = "clamp(20px, 4vw, 82px)"
  const featured = projects.filter(p => p.featured)
  const smaller  = projects.filter(p => !p.featured)
  const open = (p: Project) => navigate(`/projeto/${projSlug(p)}`)

  return (
    <>
    <section id="trabalhos" style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }} />

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad} 0`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <motion.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }} />
            <span>STUDIO TABI — PROJETOS</span>
          </div>
        </Reveal>

        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 md:gap-0" style={{ marginBottom: "clamp(32px, 5vw, 60px)" }}>
          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(40px, 5.4vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.85 }}>
            <HeadlineLine delay={0.05}>TRABALHOS</HeadlineLine>
            <HeadlineLine delay={0.12} color={RED}>SELECIONADOS.</HeadlineLine>
          </h2>
          <Reveal delay={0.18}>
            <div className="flex items-center gap-6 pb-2">
              <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.25)" }}>120+ projetos entregues</span>
              <motion.button
                style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }}
                whileHover={{ gap: "18px" } as any}
                transition={{ duration: 0.22 }}
              >
                VER TODOS <span style={{ fontSize: 13 }}>→</span>
              </motion.button>
            </div>
          </Reveal>
        </div>
      </div>

      {/* Featured 2-col grid */}
      <div
        className="grid grid-cols-1 md:grid-cols-2"
        style={{ padding: `0 ${pad}`, gap: "clamp(12px, 1.5vw, 20px)", marginBottom: "clamp(12px, 1.5vw, 20px)" }}
      >
        {featured.map((p, i) => <ProjectCard key={p.id} proj={p as Project} index={i} onClick={() => open(p as Project)} />)}
      </div>

      {/* Smaller 3-col grid (last item is a CTA card) */}
      <div
        className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3"
        style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "clamp(12px, 1.5vw, 20px)" }}
      >
        {smaller.map((p, i) => <ProjectCard key={p.id} proj={p as Project} index={i + 2} onClick={() => open(p as Project)} />)}

        {/* CTA card */}
        <Reveal delay={0.3}>
          <motion.div
            className="flex flex-col items-start justify-between"
            style={{ border: `1px solid rgba(239,239,239,0.10)`, borderRadius: 4, padding: "clamp(28px, 3vw, 40px)", aspectRatio: "1/1", cursor: "pointer", position: "relative", overflow: "hidden" }}
            whileHover={{ borderColor: RED }}
            transition={{ duration: 0.25 }}
          >
            <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(32px, 4vw, 60px)", letterSpacing: "-0.06em", color: "rgba(239,239,239,0.08)", lineHeight: 1 }}>120+</span>
            <div>
              <p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.6, color: "rgba(239,239,239,0.55)", margin: "0 0 20px" }}>
                Quer ver o portfólio completo com todos os nossos projetos?
              </p>
              <motion.button
                style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }}
                whileHover={{ gap: "18px" } as any}
                transition={{ duration: 0.22 }}
              >
                VER PORTFÓLIO <span style={{ fontSize: 13 }}>→</span>
              </motion.button>
            </div>
          </motion.div>
        </Reveal>
      </div>
    </section>
    </>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// FAQ SECTION
// ─────────────────────────────────────────────────────────────────────────────
function FaqItem({ question, answer, index, isOpen, onToggle }: { question: string; answer: string; index: number; isOpen: boolean; onToggle: () => void }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: "-40px" })

  return (
    <motion.div
      ref={ref}
      style={{ borderTop: "1px solid rgba(239,239,239,0.10)", position: "relative", overflow: "hidden" }}
      initial={{ opacity: 0, y: 20 }}
      animate={inView ? { opacity: 1, y: 0 } : {}}
      transition={{ duration: 0.7, delay: index * 0.06, ease: [0.16, 1, 0.3, 1] }}
    >
      {/* Wipe on top border when visible */}
      <motion.div
        style={{ position: "absolute", top: 0, left: 0, right: 0, height: "1px", backgroundColor: isOpen ? RED : "rgba(239,239,239,0.20)", transformOrigin: "left" }}
        initial={{ scaleX: 0 }}
        animate={inView ? { scaleX: 1 } : {}}
        transition={{ duration: 0.5, delay: index * 0.06 }}
      />

      <button
        onClick={onToggle}
        style={{ width: "100%", background: "transparent", border: "none", cursor: "pointer", padding: "clamp(22px,3vw,32px) 0", display: "flex", alignItems: "center", justifyContent: "space-between", gap: 24, textAlign: "left" }}
      >
        <div style={{ display: "flex", alignItems: "baseline", gap: 16, flex: 1 }}>
          <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.14em", color: isOpen ? RED : "rgba(239,239,239,0.30)", flexShrink: 0, paddingTop: 2 }}>
            {String(index + 1).padStart(2, "0")}
          </span>
          <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: "clamp(15px, 1.5vw, 22px)", letterSpacing: "-0.03em", textTransform: "uppercase", color: isOpen ? WHITE : "rgba(239,239,239,0.70)", lineHeight: 1.1, transition: "color 0.25s" }}>
            {question}
          </span>
        </div>
        <motion.span
          style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: 18, color: isOpen ? RED : "rgba(239,239,239,0.30)", flexShrink: 0, lineHeight: 1 }}
          animate={{ rotate: isOpen ? 45 : 0 }}
          transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
        >
          +
        </motion.span>
      </button>

      <motion.div
        style={{ overflow: "hidden" }}
        initial={{ height: 0 }}
        animate={{ height: isOpen ? "auto" : 0 }}
        transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
      >
        <p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(13px, 0.95vw, 16px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.52)", paddingBottom: "clamp(22px,3vw,32px)", paddingLeft: "clamp(0px, 2vw, 36px)", margin: 0, maxWidth: 720 }}>
          {answer}
        </p>
      </motion.div>
    </motion.div>
  )
}

function FaqSection() {
  const { content } = useContent()
  const [openIndex, setOpenIndex] = useState<number | null>(null)
  const pad = "clamp(20px, 4vw, 82px)"

  return (
    <section style={{ backgroundColor: "#0D0D0D", fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }} />

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad}`, maxWidth: 1400, margin: "0 auto" }}>
        <div className="grid grid-cols-1 md:grid-cols-12" style={{ gap: "clamp(32px, 5vw, 80px)", alignItems: "start" }}>

          {/* Left: eyebrow + headline + CTA */}
          <div className="md:col-span-4 md:sticky" style={{ top: "clamp(80px, 10vh, 120px)" }}>
            <Reveal delay={0}>
              <div className="flex items-center gap-3 mb-8" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
                <motion.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }} />
                <span>STUDIO TABI — FAQ</span>
              </div>
            </Reveal>

            <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(38px, 4.5vw, 76px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: "0 0 24px", lineHeight: 0.85 }}>
              <HeadlineLine delay={0.05}>PERGUNTAS</HeadlineLine>
              <HeadlineLine delay={0.12}>FREQUENTES</HeadlineLine>
              <HeadlineLine delay={0.19} color={RED}>.</HeadlineLine>
            </h2>

            <Reveal delay={0.25}>
              <p style={{ fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.7, color: "rgba(239,239,239,0.42)", marginBottom: 28 }}>
                Não encontrou o que procura? Entre em contato diretamente com a equipe.
              </p>
              <motion.button
                style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }}
                whileHover={{ gap: "18px" } as any}
                transition={{ duration: 0.22 }}
              >
                FALAR COM A EQUIPE <span style={{ fontSize: 13 }}>→</span>
              </motion.button>
            </Reveal>
          </div>

          {/* Right: accordion */}
          <div className="md:col-span-8">
            {content.faq.map((item, i) => (
              <FaqItem
                key={i}
                question={item.q}
                answer={item.a}
                index={i}
                isOpen={openIndex === i}
                onToggle={() => setOpenIndex(openIndex === i ? null : i)}
              />
            ))}
            {/* Bottom border */}
            <div style={{ height: "1px", backgroundColor: "rgba(239,239,239,0.10)", marginTop: 0 }} />
          </div>

        </div>
      </div>
    </section>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// FOOTER
// ─────────────────────────────────────────────────────────────────────────────
const SOCIAL_LINKS = ["Instagram", "LinkedIn", "Behance", "GitHub"]

function SiteFooter() {
  const { content } = useContent()
  const pad = "clamp(20px, 4vw, 82px)"
  const ref = useRef<HTMLElement>(null)
  const inView = useInView(ref, { once: true, margin: "-60px" })
  const footerNav = [
    { label: "Navegação", links: ["Trabalhos", "Serviços", "Sobre", "Blog", "Contato"] },
    { label: "Serviços",  links: ["Branding", "UI / UX Design", "Desenvolvimento Web", "Estratégia Digital", "Motion & Animação"] },
    { label: "Contato",   links: [content.footer.email, content.footer.phone, content.footer.city] },
  ]

  return (
    <footer id="contato" ref={ref} style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative", overflow: "hidden" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.10)" }} />

      {/* Brand mark background */}
      <div aria-hidden="true" style={{ position: "absolute", left: "-4%", bottom: "-10%", width: "clamp(240px, 34vw, 580px)", opacity: 0.04, pointerEvents: "none" }}>
        <TabiMark width="100%" color={WHITE} />
      </div>

      {/* Upper: logo + tagline + nav columns */}
      <div style={{ padding: `clamp(56px, 9vw, 100px) ${pad} clamp(40px, 6vw, 64px)`, position: "relative", zIndex: 1 }}>
        <div className="grid grid-cols-1 md:grid-cols-12" style={{ gap: "clamp(40px, 5vw, 60px)" }}>

          {/* Brand */}
          <motion.div
            className="md:col-span-4"
            initial={{ opacity: 0, y: 24 }}
            animate={inView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }}
          >
            <div style={{ marginBottom: 20 }}>
              <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 1.6vw, 26px)", letterSpacing: "-0.05em", textTransform: "uppercase", color: WHITE }}>STUDIO TABI</span>
              <span style={{ display: "block", width: 32, height: 2, backgroundColor: RED, marginTop: 10 }} />
            </div>
            <p style={{ fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.42)", maxWidth: 280, marginBottom: 28 }}>
              {content.footer.tagline}
            </p>
            <motion.button
              style={{ display: "flex", alignItems: "center", gap: 12, padding: "12px 24px", borderRadius: 999, border: `1px solid ${RED}`, color: RED, backgroundColor: "rgba(0,0,0,0)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", cursor: "pointer" }}
              whileHover={{ backgroundColor: RED, color: WHITE }}
              transition={{ duration: 0.22 }}
            >
              INICIAR PROJETO <span style={{ fontSize: 13 }}>→</span>
            </motion.button>
          </motion.div>

          {/* Nav columns */}
          {footerNav.map((col, ci) => (
            <motion.div
              key={col.label}
              className="md:col-span-2"
              style={{ minWidth: 0 }}
              initial={{ opacity: 0, y: 24 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.1 + ci * 0.08, ease: [0.16, 1, 0.3, 1] }}
            >
              <span style={{ display: "block", fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.30)", marginBottom: 20, textTransform: "uppercase" }}>
                {col.label}
              </span>
              <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 12 }}>
                {col.links.map(link => (
                  <li key={link}>
                    <motion.a
                      href="#"
                      style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, color: "rgba(239,239,239,0.50)", textDecoration: "none", display: "block", transition: "color 0.2s" }}
                      whileHover={{ color: WHITE }}
                      transition={{ duration: 0.15 }}
                    >
                      {link}
                    </motion.a>
                  </li>
                ))}
              </ul>
            </motion.div>
          ))}

          {/* Social */}
          <motion.div
            className="md:col-span-2"
            initial={{ opacity: 0, y: 24 }}
            animate={inView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.8, delay: 0.34, ease: [0.16, 1, 0.3, 1] }}
          >
            <span style={{ display: "block", fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.30)", marginBottom: 20, textTransform: "uppercase" }}>
              Social
            </span>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 12 }}>
              {SOCIAL_LINKS.map((s, si) => (
                <li key={s}>
                  <motion.a
                    href="#"
                    style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, color: "rgba(239,239,239,0.50)", textDecoration: "none", display: "flex", alignItems: "center", gap: 8 }}
                    whileHover={{ color: WHITE }}
                    transition={{ duration: 0.15, delay: 0.4 + si * 0.05 }}
                    initial={{ opacity: 0, x: -8 }}
                    animate={inView ? { opacity: 1, x: 0 } : {}}
                  >
                    {s}
                    <span style={{ fontSize: 10, opacity: 0.4 }}>↗</span>
                  </motion.a>
                </li>
              ))}
            </ul>
          </motion.div>

        </div>
      </div>

      {/* Bottom bar */}
      <div style={{ borderTop: "1px solid rgba(239,239,239,0.08)", padding: `20px ${pad}`, position: "relative", zIndex: 1 }}>
        <motion.div
          className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
          initial={{ opacity: 0 }}
          animate={inView ? { opacity: 1 } : {}}
          transition={{ duration: 0.6, delay: 0.5 }}
        >
          <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.25)" }}>
            © 2026 Studio Tabi. Todos os direitos reservados.
          </span>
          <div className="flex items-center gap-6">
            {["Política de Privacidade", "Termos de Uso"].map(item => (
              <motion.a
                key={item}
                href="#"
                style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.25)", textDecoration: "none" }}
                whileHover={{ color: "rgba(239,239,239,0.60)" }}
                transition={{ duration: 0.15 }}
              >
                {item}
              </motion.a>
            ))}
            <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.08em", color: "rgba(239,239,239,0.18)" }}>
              Feito com precisão em São Paulo
            </span>
          </div>
        </motion.div>
      </div>

    </footer>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// SUB-PAGES (Serviços, Projeto, Blog)
// ─────────────────────────────────────────────────────────────────────────────

// Cabeçalho fixo simples, reutilizado nas páginas internas.
function SubHeader() {
  const pad = "clamp(20px, 4vw, 82px)"
  const navigate = useNavigate()
  const go = (to: string) => {
    if (to.startsWith("/#")) { navigate("/"); setTimeout(() => { const el = document.getElementById(to.slice(2)); if (el) el.scrollIntoView({ behavior: "smooth" }) }, 60); return }
    navigate(to)
  }
  return (
    <header style={{ position: "sticky", top: 0, zIndex: 50, background: "rgba(17,17,17,0.85)", backdropFilter: "blur(10px)", borderBottom: "1px solid rgba(239,239,239,0.08)" }}>
      <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: `clamp(14px,2vw,20px) ${pad}` }}>
        <Link to="/" style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(13px, 1.2vw, 19px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: WHITE, textDecoration: "none" }}>
          STUDIO TABI
        </Link>
        <nav className="hidden sm:flex items-center gap-8">
          {NAV_LINKS.map(item => (
            <span key={item.label} onClick={() => go(item.to)} className="cursor-pointer" style={{ color: "rgba(239,239,239,0.55)", fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", fontFamily: '"Be Vietnam Pro", sans-serif' }}>
              {item.label}
            </span>
          ))}
        </nav>
        <Link to="/" className="sm:hidden" style={{ color: "rgba(239,239,239,0.55)", fontSize: 9, fontWeight: 600, letterSpacing: "0.13em", textDecoration: "none", fontFamily: '"Be Vietnam Pro", sans-serif' }}>
          ← INÍCIO
        </Link>
      </div>
    </header>
  )
}

// Rola ao topo sempre que a rota muda.
function ScrollTop() {
  useEffect(() => { window.scrollTo(0, 0) }, [])
  return null
}

function ServicesPage() {
  const { content } = useContent()
  const pad = "clamp(20px, 4vw, 82px)"
  return (
    <div style={{ background: BLACK, minHeight: "100svh", color: WHITE }}>
      <ScrollTop />
      <SubHeader />
      <section style={{ fontFamily: '"Be Vietnam Pro", sans-serif' }}>
        <div style={{ padding: `clamp(48px, 8vw, 110px) ${pad} 0` }}>
          <div className="flex items-center gap-3 mb-8" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <span className="block rounded-full" style={{ width: 7, height: 7, backgroundColor: RED }} />
            <span>STUDIO TABI — SERVIÇOS</span>
          </div>
          <h1 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(44px, 6vw, 96px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: "0 0 clamp(16px,2vw,24px)", lineHeight: 0.85 }}>
            O que<br />entregamos.
          </h1>
          <p style={{ maxWidth: 640, fontSize: "clamp(14px,1.1vw,18px)", lineHeight: 1.7, color: "rgba(239,239,239,0.55)", margin: "0 0 clamp(40px,6vw,72px)" }}>
            Design, estratégia e tecnologia sob um mesmo teto. Cada serviço é pensado para mover o ponteiro do seu negócio.
          </p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ padding: `0 ${pad} clamp(64px,10vw,120px)`, gap: "0 clamp(24px,3vw,48px)" }}>
          {content.services.map((s, i) => (
            <ServiceCard key={s.num} num={s.num} title={s.title} body={s.body} delay={i * 0.06} />
          ))}
        </div>
      </section>
      <SiteFooter />
    </div>
  )
}

function ProjectPage() {
  const { content } = useContent()
  const { slug } = useParams()
  const projects = content.projects
  const idx = projects.findIndex(p => projSlug(p) === slug)
  const proj = idx >= 0 ? projects[idx] : null

  if (!proj) {
    return (
      <div style={{ background: BLACK, minHeight: "100svh", color: WHITE }}>
        <ScrollTop />
        <SubHeader />
        <div style={{ padding: "clamp(80px,14vw,160px) clamp(20px,4vw,82px)", textAlign: "center", fontFamily: '"Be Vietnam Pro", sans-serif' }}>
          <p style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(28px,4vw,52px)", textTransform: "uppercase", letterSpacing: "-0.04em" }}>Projeto não encontrado</p>
          <Link to="/#trabalhos" style={{ color: RED, fontSize: 11, fontWeight: 600, letterSpacing: "0.14em", textDecoration: "none" }}>← VER TODOS OS PROJETOS</Link>
        </div>
        <SiteFooter />
      </div>
    )
  }

  const prevSlug = projSlug(projects[(idx - 1 + projects.length) % projects.length])
  const nextSlug = projSlug(projects[(idx + 1) % projects.length])

  return (
    <div style={{ background: "#080808", minHeight: "100svh", color: WHITE }}>
      <ScrollTop key={slug} />
      <SubHeader />
      <ProjectPageBody proj={proj as Project} prevSlug={prevSlug} nextSlug={nextSlug} />
      <SiteFooter />
    </div>
  )
}

// ── Blog (posts nativos do WordPress via REST, mesma origem) ──
interface WpPost {
  slug: string
  title: string
  date: string
  excerpt: string
  content: string
  image: string | null
}

function decodeEntities(s: string) {
  const t = document.createElement("textarea")
  t.innerHTML = s
  return t.value
}
function stripTags(s: string) {
  return s.replace(/<[^>]*>/g, "")
}
function formatDate(iso: string) {
  try { return new Date(iso).toLocaleDateString("pt-BR", { day: "2-digit", month: "long", year: "numeric" }) } catch { return "" }
}

function mapPost(p: any): WpPost {
  const img = p?._embedded?.["wp:featuredmedia"]?.[0]?.source_url ?? null
  return {
    slug: p.slug,
    title: decodeEntities(p.title?.rendered ?? ""),
    date: p.date ?? "",
    excerpt: decodeEntities(stripTags(p.excerpt?.rendered ?? "")).trim(),
    content: p.content?.rendered ?? "",
    image: img,
  }
}

function useWpPosts(slug?: string) {
  const [posts, setPosts] = useState<WpPost[] | null>(null)
  const [error, setError] = useState(false)
  useEffect(() => {
    let alive = true
    const q = slug
      ? `/wp-json/wp/v2/posts?_embed&slug=${encodeURIComponent(slug)}`
      : `/wp-json/wp/v2/posts?_embed&per_page=12`
    fetch(q)
      .then(r => (r.ok ? r.json() : Promise.reject()))
      .then(data => { if (alive) setPosts((Array.isArray(data) ? data : []).map(mapPost)) })
      .catch(() => { if (alive) { setPosts([]); setError(true) } })
    return () => { alive = false }
  }, [slug])
  return { posts, error }
}

const BLOG_PAD = "clamp(20px, 4vw, 82px)"

function BlogListPage() {
  const { posts } = useWpPosts()
  return (
    <div style={{ background: BLACK, minHeight: "100svh", color: WHITE, fontFamily: '"Be Vietnam Pro", sans-serif' }}>
      <ScrollTop />
      <SubHeader />
      <section style={{ padding: `clamp(48px, 8vw, 110px) ${BLOG_PAD} clamp(64px,10vw,120px)` }}>
        <div className="flex items-center gap-3 mb-8" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
          <span className="block rounded-full" style={{ width: 7, height: 7, backgroundColor: RED }} />
          <span>STUDIO TABI — BLOG</span>
        </div>
        <h1 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(44px, 6vw, 96px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: "0 0 clamp(40px,6vw,72px)", lineHeight: 0.85 }}>
          Ideias &<br />artigos.
        </h1>

        {posts === null ? (
          <p style={{ color: "rgba(239,239,239,0.4)", fontSize: 13 }}>Carregando…</p>
        ) : posts.length === 0 ? (
          <div style={{ border: "1px solid rgba(239,239,239,0.10)", borderRadius: 8, padding: "clamp(32px,5vw,56px)", maxWidth: 560 }}>
            <p style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: 20, letterSpacing: "-0.02em", margin: "0 0 8px" }}>Nenhum post publicado ainda</p>
            <p style={{ color: "rgba(239,239,239,0.5)", fontSize: 13, lineHeight: 1.6, margin: 0 }}>
              Publique posts no painel do WordPress (menu <strong>Posts</strong>) e eles aparecem aqui automaticamente.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ gap: "clamp(16px,2vw,28px)" }}>
            {posts.map((p, i) => (
              <motion.div
                key={p.slug}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.6, delay: (i % 3) * 0.06, ease: EASE_OUT_EXPO }}
              >
                <Link to={`/blog/${p.slug}`} style={{ textDecoration: "none", color: "inherit", display: "block", border: "1px solid rgba(239,239,239,0.10)", borderRadius: 8, overflow: "hidden", height: "100%" }}>
                  <div style={{ aspectRatio: "16/10", background: p.image ? `#0D0D0D center/cover no-repeat url(${p.image})` : "linear-gradient(135deg,#1A0505,#2D0A0A)" }} />
                  <div style={{ padding: "clamp(18px,2vw,24px)" }}>
                    <p style={{ margin: "0 0 10px", fontSize: 8, fontWeight: 600, letterSpacing: "0.14em", color: RED, textTransform: "uppercase" }}>{formatDate(p.date)}</p>
                    <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: "clamp(17px,1.6vw,22px)", letterSpacing: "-0.02em", lineHeight: 1.15, margin: "0 0 10px" }}>{p.title}</h2>
                    <p style={{ margin: 0, fontSize: 13, lineHeight: 1.6, color: "rgba(239,239,239,0.5)", display: "-webkit-box", WebkitLineClamp: 3, WebkitBoxOrient: "vertical", overflow: "hidden" }}>{p.excerpt}</p>
                  </div>
                </Link>
              </motion.div>
            ))}
          </div>
        )}
      </section>
      <SiteFooter />
    </div>
  )
}

function BlogPostPage() {
  const { slug } = useParams()
  const { posts } = useWpPosts(slug)
  const post = posts && posts.length ? posts[0] : null

  return (
    <div style={{ background: BLACK, minHeight: "100svh", color: WHITE, fontFamily: '"Be Vietnam Pro", sans-serif' }}>
      <ScrollTop key={slug} />
      <SubHeader />
      <article style={{ maxWidth: 760, margin: "0 auto", padding: `clamp(40px,6vw,80px) ${BLOG_PAD} clamp(64px,10vw,120px)` }}>
        <Link to="/blog" style={{ color: "rgba(239,239,239,0.4)", fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", textDecoration: "none" }}>← BLOG</Link>
        {posts === null ? (
          <p style={{ color: "rgba(239,239,239,0.4)", fontSize: 13, marginTop: 32 }}>Carregando…</p>
        ) : !post ? (
          <p style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(24px,3vw,40px)", textTransform: "uppercase", marginTop: 32 }}>Post não encontrado</p>
        ) : (
          <>
            <p style={{ margin: "28px 0 12px", fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", color: RED, textTransform: "uppercase" }}>{formatDate(post.date)}</p>
            <h1 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(30px,4.4vw,64px)", letterSpacing: "-0.04em", lineHeight: 0.95, textTransform: "uppercase", margin: "0 0 clamp(24px,3vw,40px)" }}>{post.title}</h1>
            {post.image && <img src={post.image} alt={post.title} style={{ width: "100%", borderRadius: 8, marginBottom: "clamp(24px,3vw,40px)", display: "block" }} />}
            <div className="tabi-post-body" dangerouslySetInnerHTML={{ __html: post.content }} />
          </>
        )}
      </article>
      <SiteFooter />
    </div>
  )
}

const router = createBrowserRouter([
  {
    path: "/",
    Component: Root,
    children: [
      { index: true, Component: HomeSite },
      { path: "servicos", Component: ServicesPage },
      { path: "projeto/:slug", Component: ProjectPage },
      { path: "blog", Component: BlogListPage },
      { path: "blog/:slug", Component: BlogPostPage },
      { path: "admin", Component: Admin },
    ],
  },
])

export default function App() {
  return <RouterProvider router={router} />
}
