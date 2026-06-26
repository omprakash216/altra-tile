import { ArrowRight, Check } from "lucide-react";

export function SectionHeading({
  eyebrow,
  title,
  description,
  light = false,
  centered = false,
}) {
  return (
    <div
      className={`${centered ? "mx-auto max-w-3xl text-center" : "max-w-2xl"} ${light ? "text-white" : "text-slate-950"
        }`}
    >
      <p className="eyebrow">{eyebrow}</p>
      <h2 className="mt-4 font-display text-3xl font-bold tracking-tight sm:text-4xl lg:text-[2.65rem] lg:leading-[1.16]">
        {title}
      </h2>
      {description && (
        <p
          className={`mt-5 text-base leading-7 ${light ? "text-slate-300" : "text-slate-600"
            }`}
        >
          {description}
        </p>
      )}
    </div>
  );
}

export function ArrowLink({
  children,
  href = "/contact",
  variant = "text",
  className = "",
}) {
  const variants = {
    primary: "button-primary",
    secondary: "button-secondary",
    dark: "button-dark",
    text: "inline-flex items-center gap-2 font-bold text-orange-600 transition-colors hover:text-orange-700",
  };

  return (
    <a href={href} className={`${variants[variant]} ${className}`}>
      {children}
      <ArrowRight size={18} />
    </a>
  );
}

export function CardImage({
  src,
  alt,
  className = "",
  imageClassName = "",
  tone = "light",
  padding = "p-0",
  loading = "lazy",
}) {
  const toneClasses =
    tone === "dark" ? "bg-[#0b1220]" : "bg-white";

  return (
    <div className={`relative overflow-hidden ${toneClasses} ${className}`}>
      <div className={`absolute inset-0 flex items-center justify-center ${padding}`}>
        <img
          src={src}
          alt={alt}
          className={`relative z-10 h-full w-full object-contain drop-shadow-2xl ${imageClassName}`}
          loading={loading}
        />
      </div>
    </div>
  );
}

import { MapPin } from "lucide-react";
import { Link } from "react-router-dom";

export function ProductCard({ product }) {
  return (
    <article className="card-lift group flex h-full flex-col overflow-hidden rounded-[2rem] bg-white p-2 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
      <Link to={`/category/${product.id}`} className="relative block">
        <CardImage
          src={product.image}
          alt={product.title}
          className="aspect-[1.15] rounded-[1.5rem] bg-slate-100"
          tone="light"
          padding="p-0"
        />
        <span className="absolute left-3 top-3 z-20 rounded-full bg-[#160c07]/80 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.15em] text-white backdrop-blur">
          {product.category}
        </span>
      </Link>
      <div className="flex flex-1 flex-col px-1.5 pb-2 pt-3">
        <div className="flex-1 rounded-2xl bg-[#f5f3f0] p-4 text-left shadow-inner border border-slate-200/50">
          <Link to={`/category/${product.id}`} className="block">
            <h3 className="font-display text-[1rem] font-extrabold leading-snug text-slate-900 transition-colors group-hover:text-orange-600 line-clamp-2">
              {product.title}
            </h3>
          </Link>
          <div className="mt-3 flex items-start gap-2 text-slate-500">
            <MapPin size={15} className="mt-0.5 shrink-0 text-slate-800" />
            <p className="text-[11px] font-semibold leading-[1.6] text-slate-600 line-clamp-2">
              {product.features.join(" • ")}
            </p>
          </div>
        </div>

        <div className="mt-3 flex items-center gap-2 px-1">
          <Link
            to="/contact"
            className="flex-1 rounded-full bg-orange-100 py-2.5 text-center text-[10px] font-extrabold uppercase tracking-wider text-orange-700 transition hover:bg-orange-200 hover:text-orange-900"
          >
            On Request
          </Link>
          <Link
            to={`/category/${product.id}`}
            className="flex-[1.4] rounded-full bg-slate-950 py-2.5 text-center text-[10px] font-extrabold uppercase tracking-wider text-white transition hover:bg-orange-500"
          >
            VIEW DETAILS
          </Link>
        </div>
      </div>
    </article>
  );
}
