import { useRef, useEffect, useState, useCallback, lazy, Suspense } from "react";
import { LazyMotion, domAnimation, m, AnimatePresence, useScroll, useTransform, useMotionValue, useSpring, useMotionTemplate, useInView, } from "motion/react";
import { RouterProvider, createBrowserRouter, Link, useSearchParams } from "react-router";
import { useGoTo } from "./hooks/useGoTo";
import { useContent, type SiteContent } from "./store/content";
import { fetchPosts, type PostCard } from "./store/blog";
import Root from "./Root";
import { TabiMark } from "./components/TabiMark";
import { SLUGS } from "./i18n/locale";
import { useLocale } from "./i18n/useLocale";
const ProjectDetail = lazy(() => import("./components/ProjectDetail"));
import { ProcessIcon } from "./components/ui/ProcessIcon";
import { TextReveal } from "./components/ui/TextReveal";
const lazyPage = (importer: () => Promise<{
    default: React.ComponentType;
}>) => async () => ({ Component: (await importer()).default });
const RED = "#F20C25";
const RED_BTN = "#DA0A20";
const RED_INK = "#FF3547";
const WHITE = "#EFEFEF";
const PURE_WHITE = "#FFFFFF";
const BLACK = "#111111";
const EASE_OUT_EXPO: [
    number,
    number,
    number,
    number
] = [0.16, 1, 0.3, 1];
const PRERENDERED = typeof window !== "undefined" && !!(window as unknown as {
    __PRERENDERED__?: number;
}).__PRERENDERED__;
const entrada = (inicial: Record<string, unknown>) => (PRERENDERED ? false : inicial);
function projectSlug(p: {
    id: string;
    name: string;
}): string {
    const base = (p.name || p.id || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/(^-|-$)/g, "");
    return base || p.id;
}
const TITLE_LINES = ["TRANSFORMAMOS", "A SUA MARCA", "EM EXPERIÊNCIA"];
const MOUNTAIN_PATHS = {
    haze: "M0 900 L0 480 C100 474 188 485 274 469 C360 453 436 473 516 448 C596 422 663 444 735 408 C794 378 838 344 879 303 C919 263 953 232 987 205 C1019 180 1047 187 1074 218 C1103 251 1128 274 1159 295 C1192 318 1218 308 1249 279 C1278 252 1306 255 1334 284 C1365 317 1392 337 1426 351 C1460 365 1487 350 1515 322 C1542 295 1572 306 1600 342 L1600 900 Z",
    far: "M0 900 L0 488 C108 481 199 493 292 474 C380 456 455 482 542 452 C624 424 688 448 760 411 C819 382 862 349 905 307 C948 265 984 230 1021 201 C1057 173 1087 184 1117 223 C1150 266 1178 296 1217 318 C1257 341 1288 333 1323 301 C1358 270 1387 277 1418 313 C1452 353 1483 375 1519 394 C1551 410 1577 428 1600 445 L1600 900 Z",
    middle: "M0 900 L0 494 C115 487 207 498 304 484 C399 469 479 493 567 464 C649 438 718 458 790 428 C852 403 899 376 947 336 C992 299 1030 260 1069 222 C1106 186 1139 193 1171 235 C1206 282 1238 320 1281 351 C1324 381 1362 371 1404 343 C1445 316 1479 331 1514 371 C1549 411 1576 438 1600 455 L1600 900 Z",
    front: "M0 900 L0 474 C94 450 174 439 253 452 C330 464 395 489 472 496 C553 502 620 485 684 457 C750 428 804 403 862 423 C926 445 975 486 1042 493 C1114 501 1172 461 1231 431 C1288 402 1337 386 1388 409 C1443 434 1487 459 1534 471 C1560 478 1582 483 1600 487 L1600 900 Z",
};
function lerp(a: number, b: number, t: number) {
    return a + (b - a) * Math.max(0, Math.min(1, t));
}
const RESPONSIVE_CSS = `
  
  html { scroll-behavior: smooth; }
  section[id], footer[id] { scroll-margin-top: 24px; }
  @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }

  
  .hero-scroll-zone { height: 180vh; }
  @media (max-width: 1100px) { .hero-scroll-zone { height: 150vh; } }
  @media (max-width: 767px)  { .hero-scroll-zone { height: 120vh; } }

  
  .hero-title-line {
    white-space: nowrap;
  }

  
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
`;
export function HomeSite() {
    const { content } = useContent();
    const goTo = useGoTo();
    const containerRef = useRef<HTMLDivElement>(null);
    const heroRef = useRef<HTMLElement>(null);
    const heroHeightRef = useRef(800);
    useEffect(() => {
        const el = heroRef.current;
        if (!el)
            return;
        heroHeightRef.current = el.clientHeight;
        const ro = new ResizeObserver(([e]) => { heroHeightRef.current = e.contentRect.height; });
        ro.observe(el);
        return () => ro.disconnect();
    }, []);
    const { scrollYProgress } = useScroll({
        target: containerRef,
        offset: ["start start", "end end"],
    });
    const MOON_AT = 0.66;
    const phase = useTransform(scrollYProgress, [0, 1], [0, MOON_AT]);
    const rawMX = useMotionValue(0);
    const rawMY = useMotionValue(0);
    const mx = useSpring(rawMX, { stiffness: 40, damping: 25 });
    const my = useSpring(rawMY, { stiffness: 40, damping: 25 });
    const onMouseMove = (e: React.MouseEvent<HTMLElement>) => {
        if (window.innerWidth <= 767)
            return;
        const r = heroRef.current?.getBoundingClientRect();
        if (!r)
            return;
        rawMX.set((e.clientX - r.left) / r.width - 0.5);
        rawMY.set((e.clientY - r.top) / r.height - 0.5);
    };
    const onMouseLeave = () => { rawMX.set(0); rawMY.set(0); };
    const heroBg = useTransform(phase, [0.41, 0.42], [WHITE, BLACK]);
    const blackScale = useTransform(phase, [0, 0.42], [0, 17]);
    const titleColor = useTransform(phase, [0.05, 0.14], [BLACK, WHITE]);
    const descColor = useTransform(phase, [0.05, 0.14], ["rgba(17,17,17,0.58)", "rgba(239,239,239,0.70)"]);
    const eyebrowColor = useTransform(phase, [0.05, 0.14], ["rgba(17,17,17,0.52)", "rgba(239,239,239,0.62)"]);
    const ctaColorFg = useTransform(phase, [0.05, 0.14], [RED_BTN, RED_INK]);
    const SUNSET_START = 0.30;
    const SUNSET_END = 0.65;
    const TRAVEL = 0.30;
    const redSunY = useTransform(phase, (p) => lerp(0, heroHeightRef.current * TRAVEL, (p - SUNSET_START) / (SUNSET_END - SUNSET_START)));
    const redSunOpacity = useTransform(phase, [SUNSET_START + 0.05, SUNSET_END - 0.03], [1, 0]);
    const redSunScale = useTransform(phase, [0, 0.28], [1, 1.12]);
    const redKanjiOpacity = useTransform(phase, [0.10, 0.22], [1, 0]);
    const whiteSunY = useTransform(phase, (p) => lerp(heroHeightRef.current * TRAVEL, 0, (p - SUNSET_START) / (SUNSET_END - SUNSET_START)));
    const whiteSunOpacity = useTransform(phase, [SUNSET_START + 0.02, SUNSET_END - 0.05], [0, 1]);
    const whiteSunInnerScale = useTransform(phase, [SUNSET_START, SUNSET_END], [0.78, 0.92]);
    const glowOpacity = useTransform(phase, [0.0, 0.18, 0.55, 0.68], [0.9, 1, 0.5, 0]);
    const hazeColor = useTransform(phase, [0, 0.42], ["#E3E3E3", "#3A3A3A"]);
    const farColor = useTransform(phase, [0, 0.42], ["#C7C7C7", "#282828"]);
    const middleColor = useTransform(phase, [0, 0.42], ["#777777", "#181818"]);
    const frontColor = useTransform(phase, [0, 0.42], [BLACK, "#050505"]);
    const hazeYN = useTransform(phase, [0, 0.42, 1], [0, -2, -4]);
    const farYN = useTransform(phase, [0, 0.42, 1], [0, -5, -8]);
    const middleYN = useTransform(phase, [0, 0.42, 1], [0, -9, -14]);
    const frontYN = useTransform(phase, [0, 0.42, 1], [0, -13, -20]);
    const hazeY = useMotionTemplate `${hazeYN}%`;
    const farY = useMotionTemplate `${farYN}%`;
    const middleY = useMotionTemplate `${middleYN}%`;
    const frontY = useMotionTemplate `${frontYN}%`;
    const hazeMX = useTransform(mx, (x) => x * 4);
    const farMX = useTransform(mx, (x) => x * 8);
    const middleMX = useTransform(mx, (x) => x * 15);
    const frontMX = useTransform(mx, (x) => x * 25);
    const sunMX = useTransform(mx, (x) => x * -11);
    const sunMY = useTransform(my, (y) => y * -7);
    const whiteSunMX = useTransform(mx, (x) => x * -7);
    return (<>
      <style>{RESPONSIVE_CSS}</style>

      
      <div id="top" ref={containerRef} className="hero-scroll-zone" style={{ position: "relative" }}>
        <m.section ref={heroRef} className="sticky top-0 w-full overflow-hidden isolate" style={{
            height: "100svh",
            minHeight: "600px",
            backgroundColor: heroBg,
            fontFamily: '"Be Vietnam Pro", sans-serif',
        }} onMouseMove={onMouseMove} onMouseLeave={onMouseLeave}>
          
          <m.div aria-hidden="true" className="absolute pointer-events-none" style={{
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
        }}/>

          
          <div aria-hidden="true" className="hero-sun-anchor absolute pointer-events-none" style={{ zIndex: 0, left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)" }}>
            <m.div className="w-full h-full rounded-full" style={{ backgroundColor: BLACK, scale: blackScale, transformOrigin: "center" }}/>
          </div>

          
          <div aria-hidden="true" className="absolute inset-0 overflow-hidden pointer-events-none" style={{ zIndex: 1 }}>
            
            <div className="hero-sun-anchor absolute" style={{ left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)", zIndex: 1 }}>
              <m.div style={{ x: sunMX, y: redSunY, width: "100%", height: "100%" }}>
                <m.div className="w-full h-full rounded-full flex items-center justify-center" style={{ backgroundColor: RED, scale: redSunScale, opacity: redSunOpacity }}>
                  <m.div className="hero-kanji flex items-center justify-center" style={{ opacity: redKanjiOpacity }}>
                    <TabiMark width="62%" color={WHITE}/>
                  </m.div>
                </m.div>
              </m.div>
            </div>

            
            <div className="hero-sun-anchor absolute" style={{ left: "75%", top: "43%", width: "clamp(310px, 31vw, 600px)", aspectRatio: "1/1", transform: "translate(-50%, -50%)", zIndex: 1 }}>
              <m.div style={{ x: whiteSunMX, y: whiteSunY, width: "100%", height: "100%" }}>
                <m.div className="w-full h-full rounded-full flex items-center justify-center" style={{ backgroundColor: WHITE, scale: whiteSunInnerScale, opacity: whiteSunOpacity }}>
                  <TabiMark width="48%" color={RED} opacity={0.75}/>
                </m.div>
              </m.div>
            </div>

            
            <m.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible" className="hero-mountain hero-mountain-haze absolute" style={{ left: "-4%", width: "108%", bottom: "16%", height: "39%", zIndex: 2, color: hazeColor, x: hazeMX, y: hazeY, transformOrigin: "center bottom" }}>
              <path d={MOUNTAIN_PATHS.haze} fill="currentColor"/>
            </m.svg>

            <m.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible" className="hero-mountain hero-mountain-far absolute" style={{ left: "-4%", width: "108%", bottom: "10%", height: "36%", zIndex: 3, color: farColor, x: farMX, y: farY, transformOrigin: "center bottom" }}>
              <path d={MOUNTAIN_PATHS.far} fill="currentColor"/>
            </m.svg>

            <m.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible" className="hero-mountain hero-mountain-middle absolute" style={{ left: "-4%", width: "108%", bottom: "4%", height: "31%", zIndex: 5, color: middleColor, x: middleMX, y: middleY, transformOrigin: "center bottom" }}>
              <path d={MOUNTAIN_PATHS.middle} fill="currentColor"/>
            </m.svg>

            <m.svg viewBox="0 0 1600 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" overflow="visible" className="hero-mountain hero-mountain-front absolute" style={{ left: "-4%", width: "108%", bottom: "-2px", height: "23%", zIndex: 6, color: frontColor, x: frontMX, y: frontY, transformOrigin: "center bottom" }}>
              <path d={MOUNTAIN_PATHS.front} fill="currentColor"/>
            </m.svg>
          </div>

          
          <div className="hero-content absolute top-0 left-0 bottom-0 flex flex-col justify-center items-start pointer-events-none" style={{ zIndex: 10, width: "min(46%, 780px)", padding: "clamp(96px, 13vh, 150px) 0 clamp(120px, 15vh, 165px) clamp(20px, 4vw, 82px)" }}>
            <m.div className="flex items-center gap-3 mb-7" style={{ color: eyebrowColor, fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", lineHeight: 1 }} initial={entrada({ y: 14, opacity: 0 })} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.75, delay: 0.55, ease: "easeOut" }}>
              <span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }}/>
              <span>{content.hero.eyebrow}</span>
            </m.div>

            <h1 className="hero-title m-0" style={{ fontFamily: '"Roboto Condensed", sans-serif', fontSize: "clamp(52px, 5.2vw, 100px)", fontWeight: 900, lineHeight: 0.88, letterSpacing: "-0.05em", textTransform: "uppercase", maxWidth: 760, display: "flex", flexDirection: "column", gap: "0.06em" }}>
              {content.hero.titleLines.map((line, i) => (<m.span key={line} className="hero-title-line block" style={{ color: titleColor }} initial={entrada({ y: "108%", opacity: 0 })} animate={{ y: "0%", opacity: 1 }} transition={{ duration: 1, delay: 0.25 + i * 0.07, ease: EASE_OUT_EXPO }}>
                  {line}
                </m.span>))}
              {content.hero.highlight && (<m.span className="hero-title-line block" style={{ color: titleColor, whiteSpace: "nowrap" }} initial={entrada({ y: "108%", opacity: 0 })} animate={{ y: "0%", opacity: 1 }} transition={{ duration: 1, delay: 0.46, ease: EASE_OUT_EXPO }}>
                  <strong style={{ font: "inherit", color: RED }}>{content.hero.highlight}</strong>
                </m.span>)}
            </h1>

            <m.p className="hero-desc" style={{ fontSize: "clamp(12px, 0.9vw, 16px)", fontWeight: 400, lineHeight: 1.65, color: descColor, width: "min(88%, 500px)", marginTop: 34, marginBottom: 0 }} initial={{ y: 16, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.85, delay: 0.65, ease: "easeOut" }}>
              {content.hero.description}
            </m.p>

            <m.div className="flex flex-wrap items-center gap-4 mt-9 pointer-events-auto" initial={entrada({ y: 14, opacity: 0 })} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.85, delay: 0.80, ease: "easeOut" }}>
              <m.button className="group flex items-center gap-3 rounded-full border font-semibold" style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", letterSpacing: "0.13em", padding: "13px 26px", borderColor: RED_BTN, color: RED_BTN, backgroundColor: "rgba(0,0,0,0)", cursor: "pointer" }} whileHover={{ backgroundColor: RED_BTN, color: PURE_WHITE }} transition={{ duration: 0.22 }} onClick={(e) => goTo(content.hero.ctaPrimary.url, e)}>
                {content.hero.ctaPrimary.label}
                <span className="inline-block transition-transform duration-300 group-hover:translate-x-0.5" style={{ fontSize: 13 }}>→</span>
              </m.button>

              <m.button className="hero-cta-secondary" style={{ fontSize: "9.5px", letterSpacing: "0.13em", color: ctaColorFg, backgroundColor: "transparent", border: "none", cursor: "pointer", padding: 0, fontFamily: '"Be Vietnam Pro", sans-serif', fontWeight: 600, opacity: 1, textDecorationLine: "underline", textDecorationColor: "rgba(0,0,0,0)", textUnderlineOffset: 4 }} whileHover={{ textDecorationColor: "currentColor" } as any} transition={{ duration: 0.2 }} onClick={(e) => goTo(content.hero.ctaSecondary.url, e)}>
                {content.hero.ctaSecondary.label}
              </m.button>
            </m.div>
          </div>

        </m.section>
      </div>

      <main id="conteudo">
        <AboutSection />
        <ServicesSection />
        <ProjectsSection />
        <BlogSection />
        <FaqSection />
      </main>
    </>);
}
function Reveal({ children, delay = 0, className = "" }: {
    children: React.ReactNode;
    delay?: number;
    className?: string;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-80px" });
    return (<m.div ref={ref} className={className} initial={{ opacity: 0, y: 24 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.85, delay, ease: [0.16, 1, 0.3, 1] }}>
      {children}
    </m.div>);
}
function HeadlineLine({ children, delay, color = WHITE }: {
    children: React.ReactNode;
    delay: number;
    color?: string;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-60px" });
    return (<div ref={ref} style={{
            overflow: "hidden",
            lineHeight: 0.88,
            paddingTop: "0.16em",
            paddingBottom: "0.1em",
            marginTop: "-0.16em",
            marginBottom: "-0.1em",
        }}>
      <m.span className="block" style={{ color }} initial={{ y: "130%" }} animate={inView ? { y: "0%" } : {}} transition={{ duration: 1, delay, ease: [0.16, 1, 0.3, 1] }}>
        {children}
      </m.span>
    </div>);
}
function Stat({ numeric, suffix, label, delay }: {
    numeric: number;
    suffix: string;
    label: string;
    delay: number;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-60px" });
    const mv = useMotionValue(0);
    const rounded = useTransform(mv, (v) => Math.round(v));
    useEffect(() => {
        if (!inView)
            return;
        const timeout = setTimeout(() => {
            const start = performance.now();
            const duration = 1400;
            function tick(now: number) {
                const t = Math.min((now - start) / duration, 1);
                mv.set(numeric * (1 - Math.pow(1 - t, 3)));
                if (t < 1)
                    requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        }, delay * 1000);
        return () => clearTimeout(timeout);
    }, [inView, numeric, delay, mv]);
    return (<m.div ref={ref} className="flex flex-col gap-3" initial={{ opacity: 0, y: 20 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.7, delay, ease: [0.16, 1, 0.3, 1] }}>
      <div style={{ position: "relative", height: "1px", backgroundColor: "rgba(239,239,239,0.08)", marginBottom: 20 }}>
        <m.div style={{ position: "absolute", inset: 0, backgroundColor: RED, transformOrigin: "left" }} initial={{ scaleX: 0 }} animate={inView ? { scaleX: 1 } : {}} transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }}/>
      </div>
      <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(36px, 4.5vw, 72px)", lineHeight: 0.85, letterSpacing: "-0.04em", color: WHITE, display: "flex", alignItems: "baseline", gap: "2px" }}>
        <m.span>{rounded}</m.span>
        <span>{suffix}</span>
      </span>
      <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "10px", fontWeight: 600, letterSpacing: "0.14em", color: "rgba(239,239,239,0.40)" }}>
        {label}
      </span>
    </m.div>);
}
function Pillar({ index, title, body, delay, icon, to, onNavigate }: {
    index: string;
    title: string;
    body: string;
    delay: number;
    icon?: string;
    to?: string;
    onNavigate?: (url: string, e?: {
        preventDefault?: () => void;
    }) => void;
}) {
    const ref = useRef<HTMLAnchorElement>(null);
    const inView = useInView(ref, { once: true, margin: "-50px" });
    const centered = useInView(ref, { margin: "-45% 0px -45% 0px" });
    const [hovered, setHovered] = useState(false);
    const active = hovered || centered;
    const EASE = [0.16, 1, 0.3, 1] as const;
    return (<m.a ref={ref} href={to || undefined} onClick={to && onNavigate ? (e) => onNavigate(to, e) : undefined} aria-label={to ? `Etapa: ${title}` : undefined} onHoverStart={() => setHovered(true)} onHoverEnd={() => setHovered(false)} style={{ display: "block", textDecoration: "none", color: "inherit", position: "relative", overflow: "hidden", borderTop: "1px solid rgba(239,239,239,0.10)", cursor: to ? "pointer" : "default" }} initial={{ opacity: 0 }} animate={inView ? { opacity: 1 } : {}} transition={{ duration: 0.5, delay: delay + 0.1 }}>
      
      <m.div aria-hidden="true" style={{ position: "absolute", inset: 0, background: "linear-gradient(90deg, rgba(242,12,37,0.12), rgba(242,12,37,0.03) 55%, transparent)", transformOrigin: "left", pointerEvents: "none" }} animate={{ scaleX: active ? 1 : 0, opacity: active ? 1 : 0 }} transition={{ duration: 0.5, ease: EASE }}/>
      
      <m.div aria-hidden="true" style={{ position: "absolute", left: 0, top: 0, bottom: 0, width: 3, background: RED, transformOrigin: "top", pointerEvents: "none" }} animate={{ scaleY: active ? 1 : 0 }} transition={{ duration: 0.45, ease: EASE }}/>
      
      <m.div aria-hidden="true" className="hidden sm:block" style={{ position: "absolute", right: "clamp(16px,4vw,72px)", top: "50%", width: "clamp(64px,7vw,120px)", pointerEvents: "none" }} animate={{ opacity: active ? 0.16 : 0, x: active ? 0 : 28, rotate: active ? 0 : -8, y: "-50%" }} transition={{ duration: 0.6, ease: EASE }}>
        <TabiMark width="100%" color={RED}/>
      </m.div>

      <m.div className="flex items-center gap-4 md:gap-6" style={{ position: "relative", zIndex: 1 }} animate={{ paddingTop: active ? 32 : 26, paddingBottom: active ? 32 : 26, paddingLeft: active ? 20 : 10 }} transition={{ duration: 0.4, ease: EASE }}>
        {icon && (<m.span aria-hidden="true" className="hidden sm:flex" style={{ flexShrink: 0, alignItems: "center", justifyContent: "center" }} animate={{ color: active ? RED : "rgba(239,239,239,0.4)" }} transition={{ duration: 0.35 }}>
            <ProcessIcon name={icon} size={30}/>
          </m.span>)}
        <m.span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(26px, 3vw, 46px)", lineHeight: 1, letterSpacing: "-0.05em", flexShrink: 0, width: "clamp(40px, 4vw, 70px)" }} animate={{ color: active ? RED : "rgba(239,239,239,0.18)", scale: active ? 1.06 : 1 }} transition={{ duration: 0.35 }}>
          {index}
        </m.span>
        <div className="flex flex-col gap-1.5 flex-1" style={{ minWidth: 0 }}>
          <m.h3 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 2vw, 32px)", letterSpacing: "-0.03em", textTransform: "uppercase", lineHeight: 1.02, margin: 0 }} animate={{ x: active ? 6 : 0, color: active ? WHITE : "rgba(239,239,239,0.82)" }} transition={{ duration: 0.35, ease: EASE }}>
            {title}
          </m.h3>
          <m.p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(11px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.65, margin: 0, maxWidth: 560 }} animate={{ x: active ? 6 : 0, color: active ? "rgba(239,239,239,0.72)" : "rgba(239,239,239,0.42)" }} transition={{ duration: 0.35, ease: EASE }}>
            {body}
          </m.p>
        </div>
        <m.span aria-hidden="true" style={{ fontSize: "clamp(16px, 1.6vw, 24px)", flexShrink: 0 }} animate={{ color: active ? RED : "rgba(239,239,239,0.2)", x: active ? 5 : 0 }} transition={{ duration: 0.35, ease: EASE }}>
          →
        </m.span>
      </m.div>
    </m.a>);
}
function AboutSection() {
    const { content } = useContent();
    const goTo = useGoTo();
    const { t, rota } = useLocale();
    const sec = content.sections.about;
    const sectionRef = useRef<HTMLElement>(null);
    const { scrollYProgress: sectionScroll } = useScroll({ target: sectionRef, offset: ["start end", "end start"] });
    const kanjiY = useTransform(sectionScroll, [0, 1], ["8%", "-10%"]);
    const kanjiOpacity = useTransform(sectionScroll, [0, 0.15, 0.7, 1], [0, 0.055, 0.055, 0]);
    const kanjiRotate = useTransform(sectionScroll, [0, 1], [-2, 2]);
    const STATS = content.about.stats;
    const STEPS = content.process;
    const pad = "clamp(20px, 4vw, 82px)";
    return (<section id="sobre" ref={sectionRef} style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative", overflow: "hidden" }}>

      
      <m.div aria-hidden="true" className="absolute pointer-events-none" style={{ opacity: kanjiOpacity, right: "-5%", top: "6%", y: kanjiY, rotate: kanjiRotate, width: "clamp(260px, 38vw, 680px)" }}>
        <TabiMark width="100%" color={WHITE}/>
      </m.div>

      
      <div style={{ position: "relative", width: "100%", height: "2px", backgroundColor: "rgba(239,239,239,0.08)", overflow: "hidden" }}>
        <m.div aria-hidden="true" style={{ position: "absolute", inset: 0, background: `linear-gradient(90deg, ${RED} 0%, ${RED} 55%, rgba(242,12,37,0) 100%)`, transformOrigin: "left" }} initial={{ scaleX: 0 }} whileInView={{ scaleX: 1 }} viewport={{ once: true, margin: "-8% 0px" }} transition={{ duration: 1.05, ease: [0.16, 1, 0.3, 1] }}/>
      </div>

      
      <div style={{ padding: `clamp(56px, 9vw, 130px) ${pad} 0`, position: "relative", zIndex: 1 }}>

        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <m.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }}/>
            <span>{sec.eyebrow}</span>
          </div>
        </Reveal>

        
        <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(32px, 6vw, 100px)", alignItems: "start" }}>

          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(42px, 5.4vw, 92px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, display: "flex", flexDirection: "column", gap: "0.06em" }}>
            <HeadlineLine delay={0.05}>{t("home.sobre1")}</HeadlineLine>
            <HeadlineLine delay={0.12}>{t("home.sobre2")}</HeadlineLine>
            <HeadlineLine delay={0.19} color={RED}>{t("home.sobre3")}</HeadlineLine>
            <HeadlineLine delay={0.26}>{t("home.sobre4")}</HeadlineLine>
          </h2>

          <div className="flex flex-col" style={{ gap: "clamp(24px, 4vw, 56px)" }}>
            <TextReveal text={content.about.paragraph1} delay={0.1} style={{ fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}/>
            <TextReveal text={content.about.paragraph2} delay={0.16} style={{ fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}/>
            <Reveal delay={0.24}>
              <m.button className="flex items-center gap-3 self-start" style={{ fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, backgroundColor: "transparent", border: "none", cursor: "pointer", padding: 0, fontFamily: '"Be Vietnam Pro", sans-serif' }} whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }} onClick={(e) => goTo(sec.ctaUrl, e)}>
                {sec.ctaLabel}
                <span style={{ fontSize: 13 }}>→</span>
              </m.button>
            </Reveal>
          </div>
        </div>
      </div>

      
      <div className="grid grid-cols-2 md:grid-cols-4" style={{ padding: `clamp(48px, 8vw, 110px) ${pad}`, gap: "clamp(20px, 3vw, 48px)", position: "relative", zIndex: 1 }}>
        {STATS.map((s, i) => (<Stat key={s.label} numeric={s.numeric} suffix={s.suffix} label={s.label} delay={i * 0.12}/>))}
      </div>

      <div style={{ margin: `0 ${pad}`, height: "1px", backgroundColor: "rgba(239,239,239,0.08)", position: "relative", zIndex: 1 }}/>

      
      
      <div className="steps-block" style={{ padding: `0 ${pad} clamp(64px, 10vw, 140px)`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center justify-between pt-10 md:pt-12 pb-2">
            <span style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.30)" }}>
              {sec.pillarsLabel}
            </span>
            <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.20)" }}>
              {`0${STEPS.length}`} {t("sobre.etapas")}
            </span>
          </div>
        </Reveal>


        {STEPS.map((p, i) => (<Pillar key={p.slug || p.title} index={`0${i + 1}`} title={p.title} body={p.summary} delay={i * 0.06} icon={p.icon} to={p.slug ? rota("processo", p.slug) : undefined} onNavigate={goTo}/>))}
      </div>

    </section>);
}
const SERVICES_DATA = [
    { num: "01", title: "Branding & Identidade Visual", body: "Sistemas de marca que comunicam com precisão — do logotipo ao tom de voz. Identidades que crescem com o negócio e resistem ao tempo." },
    { num: "02", title: "Design de Interface (UI/UX)", body: "Interfaces construídas a partir do comportamento real do usuário. Cada pixel tem função. Cada fluxo tem intenção." },
    { num: "03", title: "Desenvolvimento Web", body: "Código limpo, performático e acessível. Sites e aplicações que carregam rápido, escalam com o negócio e integram com qualquer stack." },
    { num: "04", title: "Estratégia Digital", body: "Diagnóstico, posicionamento e roadmap para sua presença digital. Decisões com dados, não com suposições." },
    { num: "05", title: "Motion & Animação", body: "Movimento que conta histórias. Animações de interface e motion graphics que transformam conteúdo em experiência." },
    { num: "06", title: "Conteúdo & Copywriting", body: "Palavras que convertem. Narrativas que constroem autoridade, geram confiança e movem o usuário à ação." },
];
function ServiceCard({ num, title, body, delay, to, onNavigate }: {
    num: string;
    title: string;
    body: string;
    delay: number;
    to?: string;
    onNavigate?: (url: string, e?: {
        preventDefault?: () => void;
    }) => void;
}) {
    const ref = useRef<HTMLAnchorElement>(null);
    const inView = useInView(ref, { once: true, margin: "-60px" });
    return (<m.a ref={ref} href={to || undefined} onClick={to && onNavigate ? (e) => onNavigate(to, e) : undefined} aria-label={to ? `Ver serviço: ${title}` : undefined} style={{ display: "block", textDecoration: "none", borderTop: "1px solid rgba(239,239,239,0.10)", paddingTop: "clamp(24px,3vw,36px)", paddingBottom: "clamp(24px,3vw,36px)", position: "relative", overflow: "hidden", cursor: to ? "pointer" : "default" }} initial={{ opacity: 0, y: 30 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.8, delay, ease: [0.16, 1, 0.3, 1] }}>
      
      <m.div style={{ position: "absolute", top: 0, left: 0, right: 0, height: "1px", backgroundColor: RED, transformOrigin: "left" }} initial={{ scaleX: 0 }} animate={inView ? { scaleX: 1 } : {}} transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }}/>
      <span style={{ display: "block", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.16em", color: RED_INK, marginBottom: 16 }}>{num}</span>
      <div style={{ overflow: "hidden", marginBottom: 14 }}>
        <m.h3 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 1.7vw, 28px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: WHITE, lineHeight: 0.95, margin: 0 }} initial={{ y: "105%" }} animate={inView ? { y: "0%" } : {}} transition={{ duration: 0.75, delay: delay + 0.12, ease: [0.16, 1, 0.3, 1] }}>
          {title}
        </m.h3>
      </div>
      <m.p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.65, color: "rgba(239,239,239,0.45)", margin: 0 }} initial={{ opacity: 0 }} animate={inView ? { opacity: 1 } : {}} transition={{ duration: 0.7, delay: delay + 0.22 }}>
        {body}
      </m.p>
      <m.span style={{ display: "block", marginTop: 20, fontSize: 11, color: "rgba(239,239,239,0.20)" }} initial={{ opacity: 0, x: -6 }} animate={inView ? { opacity: 1, x: 0 } : {}} transition={{ duration: 0.5, delay: delay + 0.30 }}>
        →
      </m.span>
    </m.a>);
}
function ServicesSection() {
    const { content } = useContent();
    const goTo = useGoTo();
    const { t, rota } = useLocale();
    const sec = content.sections.services;
    const pad = "clamp(20px, 4vw, 82px)";
    return (<section id="servicos" style={{ backgroundColor: "#0D0D0D", fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative", overflow: "hidden" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }}/>

      
      <div aria-hidden="true" style={{ position: "absolute", right: "-2%", top: "4%", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(120px, 22vw, 400px)", lineHeight: 1, letterSpacing: "-0.08em", color: WHITE, opacity: 0.025, userSelect: "none", pointerEvents: "none" }}>
        06
      </div>

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad} 0`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <m.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }}/>
            <span>{sec.eyebrow}</span>
          </div>
        </Reveal>

        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 md:gap-0" style={{ marginBottom: "clamp(40px, 6vw, 80px)" }}>
          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(40px, 5.4vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.85 }}>
            <HeadlineLine delay={0.05}>{t("servicos.titulo")}</HeadlineLine>
            <HeadlineLine delay={0.12}>{t("servicos.destaque")}</HeadlineLine>
          </h2>
          <Reveal delay={0.18}>
            <m.button style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10, whiteSpace: "nowrap", paddingBottom: 8 }} whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }} onClick={(e) => goTo(sec.ctaUrl, e)}>
              {sec.ctaLabel} <span style={{ fontSize: 13 }}>→</span>
            </m.button>
          </Reveal>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "0 clamp(24px, 3vw, 48px)", position: "relative", zIndex: 1 }}>
        {content.services.map((s, i) => (<ServiceCard key={s.num} num={s.num} title={s.title} body={s.body} delay={i * 0.08} to={s.slug ? rota("servico", s.slug) : undefined} onNavigate={goTo}/>))}
      </div>
    </section>);
}
const PROJECTS_DATA = [
    { id: "01", name: "Nuvem Finance", category: "Branding & UI", year: "2025", bg: "linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)", accent: RED, featured: true },
    { id: "02", name: "FlowDesk", category: "Produto SaaS", year: "2025", bg: "linear-gradient(135deg,#06061A 0%,#0A0A2D 50%,#060F1A 100%)", accent: "#5B7FFF", featured: true },
    { id: "03", name: "Maison Lux", category: "E-commerce", year: "2024", bg: "linear-gradient(135deg,#0F0D08 0%,#1A1408 50%,#0D0B06 100%)", accent: "#C4A45A", featured: false },
    { id: "04", name: "Vitalize App", category: "Mobile UI", year: "2024", bg: "linear-gradient(135deg,#060F08 0%,#081A0A 50%,#060D07 100%)", accent: "#3DBF72", featured: false },
];
const PROJECT_DETAILS: Record<string, {
    client: string;
    scope: string[];
    duration: string;
    challenge: string;
    solution: string;
    results: {
        label: string;
        value: string;
    }[];
    mockupLines: string[];
}> = {
    "01": {
        client: "Nuvem Finance",
        scope: ["Identidade Visual", "UI/UX Design", "Design System"],
        duration: "14 semanas",
        challenge: "A Nuvem Finance chegou até nós como mais uma fintech genérica em um mercado saturado — paleta azul corporativa, linguagem fria, zero diferenciação. O desafio era criar uma identidade que transmitisse solidez sem perder calor humano, e uma interface que tornasse conceitos financeiros complexos acessíveis para o usuário final.",
        solution: "Desenvolvemos uma identidade visual centrada no contraste: tipografia condensada e assertiva equilibrada com espaçamento generoso e tons terrosos que remetem a confiança sem o clichê do azul bancário. O design system foi construído para escalar com o produto, com 240+ componentes documentados e um guia de voz e tom integrado.",
        results: [
            { label: "Aumento em conversão", value: "+38%" },
            { label: "Redução em churn", value: "−22%" },
            { label: "NPS pós-redesign", value: "72" },
            { label: "Componentes no DS", value: "240+" },
        ],
        mockupLines: ["DASHBOARD", "PORTFÓLIO", "ANÁLISE", "RELATÓRIOS"],
    },
    "02": {
        client: "FlowDesk",
        scope: ["Produto SaaS", "UX Research", "Prototipação", "Dev Front-end"],
        duration: "22 semanas",
        challenge: "A FlowDesk tinha uma ideia sólida de produto — uma plataforma de gestão de tarefas focada em equipes assíncronas — mas o MVP inicial tinha uma curva de aprendizado altíssima. A taxa de abandono na primeira semana era de 67%. Precisávamos reconstruir a experiência do zero, sem perder os usuários existentes.",
        solution: "Realizamos 18 entrevistas em profundidade com usuários reais e mapeamos os principais pontos de atrito. Redesenhamos o onboarding com uma abordagem de 'progressive disclosure', introduzindo funcionalidades gradualmente conforme o usuário ganha confiança. A nova arquitetura de informação reduziu os caminhos críticos de 7 para 3 cliques.",
        results: [
            { label: "Redução de abandono", value: "−51%" },
            { label: "Tempo médio no app", value: "+2.4×" },
            { label: "Usuários ativos/mês", value: "12k+" },
            { label: "Avaliação App Store", value: "4.8★" },
        ],
        mockupLines: ["PROJETOS", "TAREFAS", "EQUIPE", "RELATÓRIO"],
    },
    "03": {
        client: "Maison Lux",
        scope: ["E-commerce", "UI Design", "Motion Design"],
        duration: "10 semanas",
        challenge: "Uma marca de moda de luxo brasileira com atelier próprio mas presença digital completamente desalinhada com o posicionamento premium. O site anterior parecia uma loja de departamentos, não uma maison. As taxas de conversão estavam abaixo da média do setor para o ticket médio praticado.",
        solution: "Criamos uma experiência editorial inspirada nas grandes maisons europeias: fotografia fullscreen, tipografia serif com muito espaço branco, e microinterações que reforçam a percepção de exclusividade. O checkout foi simplificado para 2 etapas e a página de produto redesenhada para destacar artesanato e materiais.",
        results: [
            { label: "Aumento no ticket médio", value: "+29%" },
            { label: "Taxa de conversão", value: "+44%" },
            { label: "Tempo na página produto", value: "+3.1min" },
            { label: "Retorno de clientes", value: "+61%" },
        ],
        mockupLines: ["COLEÇÃO", "ATELIÊ", "PEÇAS", "CONTATO"],
    },
    "04": {
        client: "Vitalize",
        scope: ["Mobile UI", "iOS & Android", "Ilustração"],
        duration: "8 semanas",
        challenge: "O app de saúde e bem-estar Vitalize enfrentava um paradoxo: usuários adoravam o conceito mas achavam o app 'pesado' e 'intimidador'. O design anterior usava verde clínico e iconografia médica que afastava justamente o público-alvo — pessoas que queriam começar uma jornada de saúde sem sentir que tinham uma doença.",
        solution: "Redesenhamos com uma abordagem de 'saúde como estilo de vida': paleta orgânica, ilustrações feitas à mão que humanizam os dados, e um sistema de progresso gamificado que celebra pequenas vitórias. O onboarding foi transformado em uma conversa, não um formulário.",
        results: [
            { label: "Downloads no primeiro mês", value: "48k" },
            { label: "Retenção em 30 dias", value: "71%" },
            { label: "Avaliação nas stores", value: "4.9★" },
            { label: "Menções espontâneas", value: "+180%" },
        ],
        mockupLines: ["INÍCIO", "TREINOS", "NUTRIÇÃO", "PROGRESSO"],
    },
};
type Project = SiteContent["projects"][0];
function ProjectCard({ proj, index, onClick }: {
    proj: Project;
    index: number;
    onClick: () => void;
}) {
    const { t } = useLocale();
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-80px" });
    const [hovered, setHovered] = useState(false);
    return (<m.div ref={ref} data-cursor data-cursor-label={t("geral.ver")} style={{ position: "relative", overflow: "hidden", borderRadius: 4, cursor: "pointer", aspectRatio: proj.featured ? "4/3" : "1/1" }} initial={{ opacity: 0, y: 40 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.9, delay: index * 0.1, ease: [0.16, 1, 0.3, 1] }} onHoverStart={() => setHovered(true)} onHoverEnd={() => setHovered(false)} onClick={onClick}>
      
      <div style={{ position: "absolute", inset: 0, background: proj.bg }}/>

      
      {proj.imageUrl && (<m.img src={proj.imageUrl} alt={proj.name} loading="lazy" style={{ position: "absolute", inset: 0, width: "100%", height: "100%", objectFit: "cover" }} initial={false} animate={{ scale: hovered ? 1.05 : 1 }} transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}/>)}
      {proj.imageUrl && (<div aria-hidden="true" style={{ position: "absolute", inset: 0, background: "linear-gradient(to top, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.12) 45%, rgba(0,0,0,0.25) 100%)" }}/>)}

      
      <m.div style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 60% 50%, ${proj.accent}22 0%, transparent 65%)` }} animate={{ opacity: hovered ? 1.4 : 0.7 }} transition={{ duration: 0.4 }}/>

      
      <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px)`, backgroundSize: "40px 40px" }}/>

      
      <div style={{ position: "absolute", top: "clamp(20px,3vw,32px)", right: "clamp(20px,3vw,32px)", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(48px,6vw,90px)", lineHeight: 1, letterSpacing: "-0.08em", color: WHITE, opacity: 0.06, userSelect: "none" }}>
        {proj.id}
      </div>

      
      <m.div style={{ position: "absolute", top: "clamp(20px,3vw,32px)", left: "clamp(20px,3vw,32px)", width: 8, height: 8, borderRadius: "50%", backgroundColor: proj.accent }} animate={{ scale: hovered ? 1.4 : 1 }} transition={{ duration: 0.3 }}/>

      
      <div style={{ position: "absolute", bottom: 0, left: 0, right: 0, padding: "clamp(20px,3vw,32px)", background: "linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%)" }}>
        <div style={{ overflow: "visible", paddingTop: 6 }}>
          <m.h3 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(18px, 2vw, 32px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: WHITE, margin: 0, lineHeight: 1.08, paddingBottom: 2 }} animate={{ y: hovered ? -4 : 0 }} transition={{ duration: 0.3 }}>
            {proj.name}
          </m.h3>
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

      
      <m.div style={{ position: "absolute", inset: 0, border: `1px solid ${proj.accent}`, borderRadius: 4, pointerEvents: "none" }} animate={{ opacity: hovered ? 0.5 : 0 }} transition={{ duration: 0.3 }}/>
    </m.div>);
}
function ProjectsSection() {
    const { content } = useContent();
    const goTo = useGoTo();
    const { t } = useLocale();
    const sec = content.sections.projects;
    const projects = content.projects;
    const pad = "clamp(20px, 4vw, 82px)";
    const flagged = projects.filter(p => p.home);
    const homeProjects = (flagged.length ? flagged : projects).slice(0, 4);
    const featured = homeProjects.filter(p => p.featured);
    const smaller = homeProjects.filter(p => !p.featured);
    const [searchParams, setSearchParams] = useSearchParams();
    const selectedKey = searchParams.get("projeto");
    const selectedProj = projects.find(p => projectSlug(p) === selectedKey || p.id === selectedKey) ?? null;
    const openProject = useCallback((p: SiteContent["projects"][number], replace = false) => {
        setSearchParams(prev => {
            const next = new URLSearchParams(prev);
            next.set("projeto", projectSlug(p));
            return next;
        }, { replace });
    }, [setSearchParams]);
    const handleClose = useCallback(() => {
        setSearchParams(prev => {
            const next = new URLSearchParams(prev);
            next.delete("projeto");
            return next;
        }, { replace: false });
    }, [setSearchParams]);
    const handlePrev = useCallback(() => {
        if (!selectedProj)
            return;
        const idx = projects.findIndex(p => p.id === selectedProj.id);
        openProject(projects[(idx - 1 + projects.length) % projects.length], true);
    }, [selectedProj, projects, openProject]);
    const handleNext = useCallback(() => {
        if (!selectedProj)
            return;
        const idx = projects.findIndex(p => p.id === selectedProj.id);
        openProject(projects[(idx + 1) % projects.length], true);
    }, [selectedProj, projects, openProject]);
    return (<>
    <AnimatePresence>
      {selectedProj && (<Suspense fallback={null}><ProjectDetail key={selectedProj.id} proj={selectedProj} onClose={handleClose} onPrev={handlePrev} onNext={handleNext}/></Suspense>)}
    </AnimatePresence>
    <section id="trabalhos" style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }}/>

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad} 0`, position: "relative", zIndex: 1 }}>
        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <m.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }}/>
            <span>{sec.eyebrow}</span>
          </div>
        </Reveal>

        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 md:gap-0" style={{ marginBottom: "clamp(32px, 5vw, 60px)" }}>
          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(40px, 5.4vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.85 }}>
            <HeadlineLine delay={0.05}>{t("home.projetos1")}</HeadlineLine>
            <HeadlineLine delay={0.12} color={RED}>{t("home.projetos2")}</HeadlineLine>
          </h2>
          <Reveal delay={0.18}>
            <div className="flex items-center gap-6 pb-2">
              <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.25)" }}>{sec.note}</span>
              <m.button style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }} whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }} onClick={(e) => goTo(content.projectsCta.url, e)}>
                {content.projectsCta.label} <span style={{ fontSize: 13 }}>→</span>
              </m.button>
            </div>
          </Reveal>
        </div>
      </div>

      
      <div className="grid grid-cols-1 md:grid-cols-2" style={{ padding: `0 ${pad}`, gap: "clamp(12px, 1.5vw, 20px)", marginBottom: "clamp(12px, 1.5vw, 20px)" }}>
        {featured.map((p, i) => <ProjectCard key={p.id} proj={p as Project} index={i} onClick={() => openProject(p)}/>)}
      </div>

      
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3" style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "clamp(12px, 1.5vw, 20px)" }}>
        {smaller.map((p, i) => <ProjectCard key={p.id} proj={p as Project} index={i + 2} onClick={() => openProject(p)}/>)}

        
        <Reveal delay={0.3}>
          <m.div className="flex flex-col items-start justify-between" style={{ border: `1px solid rgba(239,239,239,0.10)`, borderRadius: 4, padding: "clamp(28px, 3vw, 40px)", aspectRatio: "1/1", cursor: "pointer", position: "relative", overflow: "hidden" }} whileHover={{ borderColor: RED }} transition={{ duration: 0.25 }} onClick={(e) => goTo(content.projectsCta.url, e)}>
            <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(32px, 4vw, 60px)", letterSpacing: "-0.06em", color: "rgba(239,239,239,0.08)", lineHeight: 1 }}>120+</span>
            <div>
              <p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(13px, 1vw, 17px)", fontWeight: 400, lineHeight: 1.6, color: "rgba(239,239,239,0.55)", margin: "0 0 20px" }}>
                {sec.cardText}
              </p>
              <m.button style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }} whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }} onClick={(e) => goTo(content.projectsCta.url, e)}>
                {content.projectsCta.label} <span style={{ fontSize: 13 }}>→</span>
              </m.button>
            </div>
          </m.div>
        </Reveal>
      </div>
    </section>
    </>);
}
function BlogCard({ post, index }: {
    post: PostCard;
    index: number;
}) {
    const { t, rota } = useLocale();
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-80px" });
    const [hovered, setHovered] = useState(false);
    return (<m.div ref={ref} initial={{ opacity: 0, y: 40 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.9, delay: index * 0.1, ease: [0.16, 1, 0.3, 1] }} onHoverStart={() => setHovered(true)} onHoverEnd={() => setHovered(false)}>
      <Link to={rota("artigo", post.slug)} data-cursor data-cursor-label={t("geral.ler")} style={{ display: "flex", flexDirection: "column", height: "100%", textDecoration: "none", color: "inherit" }}>
        
        <div style={{ position: "relative", overflow: "hidden", borderRadius: 4, aspectRatio: "16/10", background: "rgba(239,239,239,0.05)" }}>
          {post.image ? (<m.img src={post.image} alt="" loading="lazy" decoding="async" style={{ position: "absolute", inset: 0, width: "100%", height: "100%", objectFit: "cover" }} initial={false} animate={{ scale: hovered ? 1.06 : 1 }} transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}/>) : (<div aria-hidden="true" style={{ position: "absolute", inset: 0, display: "flex", alignItems: "center", justifyContent: "center", opacity: 0.10 }}>
              <TabiMark width="42%" color={WHITE}/>
            </div>)}
          <m.div aria-hidden="true" style={{ position: "absolute", inset: 0, background: `linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 60%)` }} animate={{ opacity: hovered ? 1 : 0.7 }} transition={{ duration: 0.3 }}/>
          
          <m.div aria-hidden="true" style={{ position: "absolute", left: 0, bottom: 0, height: 3, background: RED }} initial={false} animate={{ width: hovered ? "100%" : "0%" }} transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}/>
        </div>

        
        <div style={{ paddingTop: 18, display: "flex", flexDirection: "column", flex: 1 }}>
          <div className="flex items-center gap-3" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.13em", color: "rgba(239,239,239,0.35)", textTransform: "uppercase", marginBottom: 12 }}>
            {post.categories[0] && <span style={{ color: RED_INK }}>{post.categories[0].name}</span>}
            {post.categories[0] && <span aria-hidden="true">·</span>}
            <time dateTime={post.dateISO}>{post.date}</time>
            {post.readingTime > 0 && <><span aria-hidden="true">·</span><span>{post.readingTime} min</span></>}
          </div>
          <m.h3 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(19px, 1.7vw, 27px)", letterSpacing: "-0.035em", textTransform: "uppercase", lineHeight: 1.04, margin: "0 0 10px" }} animate={{ color: hovered ? RED_INK : WHITE }} transition={{ duration: 0.25 }}>
            {post.title}
          </m.h3>
          <p style={{ fontSize: "clamp(12px, 0.92vw, 14.5px)", lineHeight: 1.65, color: "rgba(239,239,239,0.50)", margin: 0 }}>
            {post.excerpt}
          </p>
        </div>
      </Link>
    </m.div>);
}
function BlogSection() {
    const { content } = useContent();
    const { locale, rota } = useLocale();
    const sec = content.sections.blog;
    const pad = "clamp(20px, 4vw, 82px)";
    const [posts, setPosts] = useState<PostCard[]>([]);
    useEffect(() => {
        let alive = true;
        fetchPosts({ perPage: 3, lang: locale }).then(r => { if (alive)
            setPosts(r.items); });
        return () => { alive = false; };
    }, [locale]);
    if (!posts.length)
        return null;
    return (<section id="blog" style={{ backgroundColor: BLACK, fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }}/>

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad} clamp(64px, 10vw, 120px)` }}>
        <Reveal delay={0}>
          <div className="flex items-center gap-3 mb-8 md:mb-12" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <m.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }}/>
            <span>{sec.eyebrow}</span>
          </div>
        </Reveal>

        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 md:gap-0" style={{ marginBottom: "clamp(32px, 5vw, 60px)" }}>
          <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(40px, 5.4vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.85 }}>
            <HeadlineLine delay={0.05}>{sec.title}</HeadlineLine>
            <HeadlineLine delay={0.12} color={RED}>{sec.highlight}</HeadlineLine>
          </h2>
          <Reveal delay={0.18}>
            <div className="flex items-center gap-6 pb-2">
              <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.25)", maxWidth: 300 }}>{sec.note}</span>
              <Link to={rota("blog")} style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, textDecoration: "none", display: "flex", alignItems: "center", gap: 10, whiteSpace: "nowrap" }}>
                {sec.ctaLabel} <span aria-hidden="true" style={{ fontSize: 13 }}>→</span>
              </Link>
            </div>
          </Reveal>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3" style={{ gap: "clamp(20px, 2.4vw, 34px)" }}>
          {posts.map((p, i) => <BlogCard key={p.id} post={p} index={i}/>)}
        </div>
      </div>
    </section>);
}
function AllProjects() {
    const { content } = useContent();
    const { t } = useLocale();
    const projects = content.projects;
    const pad = "clamp(20px, 4vw, 82px)";
    const [searchParams, setSearchParams] = useSearchParams();
    const selectedKey = searchParams.get("projeto");
    const selectedProj = projects.find(p => projectSlug(p) === selectedKey || p.id === selectedKey) ?? null;
    useEffect(() => {
        document.title = `${t("menu.projetos")} — ${content.site.title}`;
        if (!selectedKey)
            window.scrollTo(0, 0);
    }, [content.site.title, selectedKey, t]);
    const openProject = useCallback((p: SiteContent["projects"][number], replace = false) => {
        setSearchParams(prev => {
            const next = new URLSearchParams(prev);
            next.set("projeto", projectSlug(p));
            return next;
        }, { replace });
    }, [setSearchParams]);
    const handleClose = useCallback(() => {
        setSearchParams(prev => {
            const next = new URLSearchParams(prev);
            next.delete("projeto");
            return next;
        }, { replace: false });
    }, [setSearchParams]);
    const handlePrev = useCallback(() => {
        if (!selectedProj)
            return;
        const idx = projects.findIndex(p => p.id === selectedProj.id);
        openProject(projects[(idx - 1 + projects.length) % projects.length], true);
    }, [selectedProj, projects, openProject]);
    const handleNext = useCallback(() => {
        if (!selectedProj)
            return;
        const idx = projects.findIndex(p => p.id === selectedProj.id);
        openProject(projects[(idx + 1) % projects.length], true);
    }, [selectedProj, projects, openProject]);
    return (<div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: '"Be Vietnam Pro", sans-serif' }}>
      <AnimatePresence>
        {selectedProj && (<Suspense fallback={null}><ProjectDetail key={selectedProj.id} proj={selectedProj} onClose={handleClose} onPrev={handlePrev} onNext={handleNext}/></Suspense>)}
      </AnimatePresence>

      <main id="conteudo">
        
        <div style={{ padding: `clamp(48px, 8vw, 96px) ${pad} clamp(28px, 4vw, 48px)` }}>
          <div className="flex items-center gap-3 mb-6" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }}/>
            <span>{content.sections.projects.eyebrow}</span>
          </div>
          <h1 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(44px, 7vw, 96px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.92 }}>
            {t("projetos.titulo")} <span style={{ color: RED }}>{t("projetos.destaque")}</span>
          </h1>
        </div>

        
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3" style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "clamp(12px, 1.5vw, 20px)" }}>
          {projects.map((p, i) => (<ProjectCard key={p.id} proj={p as Project} index={i} onClick={() => openProject(p)}/>))}
        </div>
      </main>
    </div>);
}
const FAQ_DATA = [
    { q: "Como funciona o processo de trabalho?", a: "Iniciamos com um diagnóstico aprofundado do negócio, mercado e objetivos. Em seguida, criamos um roadmap claro com entregas, prazos e marcos de aprovação. Trabalhamos em sprints curtos com checkpoints semanais para garantir alinhamento contínuo — sem surpresas no final." },
    { q: "Quanto tempo leva um projeto?", a: "Depende do escopo. Um projeto de identidade visual leva de 3 a 6 semanas. Um site completo com design e desenvolvimento, de 6 a 12 semanas. Aplicações mais complexas podem levar de 3 a 6 meses. Sempre apresentamos um cronograma detalhado antes de iniciar." },
    { q: "Vocês trabalham com empresas de qual tamanho?", a: "Atendemos desde startups em fase de crescimento até empresas consolidadas que querem renovar sua presença digital. O que importa não é o tamanho, mas o comprometimento com qualidade e a disposição para construir algo duradouro." },
    { q: "Como é estruturada a precificação?", a: "Trabalhamos com projetos fechados (escopo e valor definidos no início) ou retainer mensal para empresas que precisam de parceria contínua. Não cobramos por hora — cobramos pelo resultado. O orçamento é apresentado de forma transparente, sem taxas ocultas." },
    { q: "Vocês oferecem suporte após a entrega?", a: "Sim. Todos os projetos incluem um período de garantia de 30 dias após o lançamento. Para clientes que desejam suporte contínuo, oferecemos planos de manutenção mensal que incluem atualizações, monitoramento e evolução do produto." },
    { q: "Como posso começar a trabalhar com vocês?", a: "Preencha o formulário de contato ou nos envie um e-mail com um breve contexto do seu projeto. Agendaremos uma chamada de diagnóstico gratuita de 30 minutos para entender suas necessidades e verificar se somos o parceiro certo para você." },
];
function FaqItem({ question, answer, index, isOpen, onToggle }: {
    question: string;
    answer: string;
    index: number;
    isOpen: boolean;
    onToggle: () => void;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-40px" });
    return (<m.div ref={ref} style={{ borderTop: "1px solid rgba(239,239,239,0.10)", position: "relative", overflow: "hidden" }} initial={{ opacity: 0, y: 20 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.7, delay: index * 0.06, ease: [0.16, 1, 0.3, 1] }}>
      
      <m.div style={{ position: "absolute", top: 0, left: 0, right: 0, height: "1px", backgroundColor: isOpen ? RED : "rgba(239,239,239,0.20)", transformOrigin: "left" }} initial={{ scaleX: 0 }} animate={inView ? { scaleX: 1 } : {}} transition={{ duration: 0.5, delay: index * 0.06 }}/>

      <button onClick={onToggle} style={{ width: "100%", background: "transparent", border: "none", cursor: "pointer", padding: "clamp(22px,3vw,32px) 0", display: "flex", alignItems: "center", justifyContent: "space-between", gap: 24, textAlign: "left" }}>
        <div style={{ display: "flex", alignItems: "baseline", gap: 16, flex: 1 }}>
          <span style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9px", fontWeight: 600, letterSpacing: "0.14em", color: isOpen ? RED : "rgba(239,239,239,0.30)", flexShrink: 0, paddingTop: 2 }}>
            {String(index + 1).padStart(2, "0")}
          </span>
          <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: "clamp(15px, 1.5vw, 22px)", letterSpacing: "-0.03em", textTransform: "uppercase", color: isOpen ? WHITE : "rgba(239,239,239,0.70)", lineHeight: 1.1, transition: "color 0.25s" }}>
            {question}
          </span>
        </div>
        <m.span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: 18, color: isOpen ? RED : "rgba(239,239,239,0.30)", flexShrink: 0, lineHeight: 1 }} animate={{ rotate: isOpen ? 45 : 0 }} transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}>
          +
        </m.span>
      </button>

      <m.div style={{ overflow: "hidden" }} initial={{ height: 0 }} animate={{ height: isOpen ? "auto" : 0 }} transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}>
        <p style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "clamp(13px, 0.95vw, 16px)", fontWeight: 400, lineHeight: 1.72, color: "rgba(239,239,239,0.52)", paddingBottom: "clamp(22px,3vw,32px)", paddingLeft: "clamp(0px, 2vw, 36px)", margin: 0, maxWidth: 720 }}>
          {answer}
        </p>
      </m.div>
    </m.div>);
}
function FaqSection() {
    const { content } = useContent();
    const goTo = useGoTo();
    const { t } = useLocale();
    const sec = content.sections.faq;
    const [openIndex, setOpenIndex] = useState<number | null>(null);
    const pad = "clamp(20px, 4vw, 82px)";
    return (<section style={{ backgroundColor: "#0D0D0D", fontFamily: '"Be Vietnam Pro", sans-serif', position: "relative" }}>
      <div style={{ width: "100%", height: "1px", backgroundColor: "rgba(239,239,239,0.06)" }}/>

      <div style={{ padding: `clamp(56px, 9vw, 120px) ${pad}`, maxWidth: 1400, margin: "0 auto" }}>
        <div className="grid grid-cols-1 md:grid-cols-12" style={{ gap: "clamp(32px, 5vw, 80px)", alignItems: "start" }}>

          
          <div className="md:col-span-4 faq-aside">
            <Reveal delay={0}>
              <div className="flex items-center gap-3 mb-8" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
                <m.span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} initial={{ scale: 0 }} whileInView={{ scale: 1 }} viewport={{ once: true }} transition={{ duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }}/>
                <span>{sec.eyebrow}</span>
              </div>
            </Reveal>

            
            <h2 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(38px, 4.5vw, 64px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: "0 0 24px", lineHeight: 0.85 }}>
              <HeadlineLine delay={0.05}>{t("faq.titulo")}</HeadlineLine>
              
              <HeadlineLine delay={0.12}>{t("faq.destaque")}<span style={{ color: RED }}>.</span></HeadlineLine>
            </h2>

            <Reveal delay={0.25}>
              <p style={{ fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, lineHeight: 1.7, color: "rgba(239,239,239,0.42)", marginBottom: 28 }}>
                {sec.note}
              </p>
              <m.button style={{ fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: "9.5px", fontWeight: 600, letterSpacing: "0.13em", color: RED_INK, background: "transparent", border: "none", cursor: "pointer", padding: 0, display: "flex", alignItems: "center", gap: 10 }} whileHover={{ gap: "18px" } as any} transition={{ duration: 0.22 }} onClick={(e) => goTo(sec.ctaUrl, e)}>
                {sec.ctaLabel} <span style={{ fontSize: 13 }}>→</span>
              </m.button>
            </Reveal>
          </div>

          
          <div className="md:col-span-8">
            {content.faq.map((item, i) => (<FaqItem key={i} question={item.q} answer={item.a} index={i} isOpen={openIndex === i} onToggle={() => setOpenIndex(openIndex === i ? null : i)}/>))}
            
            <div style={{ height: "1px", backgroundColor: "rgba(239,239,239,0.10)", marginTop: 0 }}/>
          </div>

        </div>
      </div>
    </section>);
}
const FOOTER_NAV = [
    { label: "Navegação", links: ["Trabalhos", "Serviços", "Sobre", "Blog", "Contato"] },
    { label: "Serviços", links: ["Branding", "UI / UX Design", "Desenvolvimento Web", "Estratégia Digital", "Motion & Animação"] },
    { label: "Contato", links: ["oi@studiotabi.com.br", "+55 11 99999-9999", "São Paulo, Brasil"] },
];
const paginas = (l: "pt" | "en") => {
    const S = (k: keyof typeof SLUGS) => SLUGS[k][l];
    return [
        { index: true, Component: HomeSite },
        { path: S("projetos"), lazy: async () => ({ Component: AllProjects }) },
        { path: S("sobre"), lazy: lazyPage(() => import("./pages/About")) },
        { path: S("servicos"), lazy: lazyPage(() => import("./pages/Services")) },
        { path: `${S("servico")}/:slug`, lazy: lazyPage(() => import("./pages/ServiceDetail")) },
        { path: `${S("processo")}/:slug`, lazy: lazyPage(() => import("./pages/ProcessStep")) },
        { path: S("blog"), lazy: lazyPage(() => import("./pages/Blog")) },
        { path: `${S("artigo")}/:slug`, lazy: lazyPage(() => import("./pages/Article")) },
        { path: S("contato"), lazy: lazyPage(() => import("./pages/Contact")) },
        { path: S("obrigado"), lazy: lazyPage(() => import("./pages/ThankYou")) },
        { path: "admin", lazy: lazyPage(() => import("./pages/Admin")) },
        { path: `${S("pagina")}/:slug`, lazy: lazyPage(() => import("./pages/Page")) },
        { path: "*", lazy: lazyPage(() => import("./pages/NotFound")) },
    ];
};
const router = createBrowserRouter([
    { path: "/en", Component: Root, children: paginas("en") },
    { path: "/", Component: Root, children: paginas("pt") },
]);
export default function App() {
    return (<LazyMotion features={domAnimation}>
      <RouterProvider router={router}/>
    </LazyMotion>);
}
