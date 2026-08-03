import { m, useTransform, useReducedMotion, type MotionValue } from "motion/react";
import { TabiMark } from "../TabiMark";

const RED = "#F20C25";

/**
 * O 旅 acompanha a leitura na ponta da barra. Quem lê o artigo inteiro vê a
 * marca atravessar a tela — a assinatura vira o próprio indicador.
 */
export function ProgressoTabi({ progresso }: {
    progresso: MotionValue<number>;
}) {
    const reduzido = useReducedMotion();
    const x = useTransform(progresso, (p) => `calc(${Math.min(1, Math.max(0, p)) * 100}vw - 11px)`);
    const rotate = useTransform(progresso, [0, 1], [0, 90]);
    // Some no começo para não pousar sobre o breadcrumb antes de haver leitura.
    const opacity = useTransform(progresso, [0, 0.02, 0.98, 1], [0, 1, 1, 0]);

    if (reduzido)
        return null;

    return (<m.div aria-hidden="true" style={{
            position: "fixed", top: 3, left: 0, zIndex: 61, pointerEvents: "none",
            x, opacity, width: 22,
        }}>
      <m.div style={{ rotate, transformOrigin: "center 3px" }}>
        <TabiMark width={22} color={RED}/>
      </m.div>
    </m.div>);
}
