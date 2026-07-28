import { useNavigate } from "react-router";
import { trackEvent } from "../utils/analytics";
import { scrollToEl } from "../components/system/SmoothScroll";
export function useGoTo() {
    const navigate = useNavigate();
    return (url: string, e?: {
        preventDefault?: () => void;
    }) => {
        if (!url)
            return;
        trackEvent("cta_click", { link_url: url });
        if (/^https?:\/\//i.test(url) || url.startsWith("mailto:") || url.startsWith("tel:")) {
            window.open(url, "_blank", "noopener");
            return;
        }
        if (e?.preventDefault)
            e.preventDefault();
        if (url.startsWith("#")) {
            const el = document.querySelector(url);
            if (el)
                scrollToEl(el as HTMLElement);
            else
                navigate("/" + url);
        }
        else {
            navigate(url);
        }
    };
}
