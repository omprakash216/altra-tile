import { useEffect, useRef, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  ArrowRight,
  ChevronDown,
  Mail,
  Menu,
  Phone,
  X,
  Facebook,
  Twitter,
  Linkedin,
  Youtube,
  Instagram,
} from "lucide-react";
import { fetchCategories } from "../api";
import { getFallbackCategories } from "../data/catalogData";
import LogoIcon from "./LogoIcon";

const links = [
  { label: "Home", href: "/" },
  { label: "Solutions", href: "/solutions" },
  { label: "Services", href: "/services" },
  { label: "About Us", href: "/about" },
  // { label: "Resources", href: "/projects" },
  { label: "Contact", href: "/contact" },
];

function Logo() {
  return (
    <Link to="/" className="flex items-center" aria-label="ULTRA TILE MACHINE home">
      <img src="/assets/logo.jpeg" alt="ULTRA Tile Machine Logo" className="h-[92px] w-auto object-contain rounded-lg shadow-sm" />
    </Link>
  );
}

export default function Navbar() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [mobileProducts, setMobileProducts] = useState(false);
  const [expandedMobileCategory, setExpandedMobileCategory] = useState(null);
  const [megaOpen, setMegaOpen] = useState(false);
  const [hoveredCategoryIndex, setHoveredCategoryIndex] = useState(0);
  const [productCategories, setProductCategories] = useState(() => getFallbackCategories());

  const location = useLocation();
  const currentPath = location.pathname;

  const desktopMenuRef = useRef(null);

  useEffect(() => {
    fetchCategories().then((data) => {
      if (Array.isArray(data) && data.length) setProductCategories(data);
    });
  }, []);

  useEffect(() => {
    if (hoveredCategoryIndex >= productCategories.length) {
      setHoveredCategoryIndex(0);
    }
  }, [hoveredCategoryIndex, productCategories.length]);

  useEffect(() => {
    document.body.style.overflow = mobileOpen ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [mobileOpen]);

  useEffect(() => {
    const closeMenu = (event) => {
      if (desktopMenuRef.current && !desktopMenuRef.current.contains(event.target)) {
        setMegaOpen(false);
      }
    };
    const closeOnEscape = (event) => {
      if (event.key === "Escape") {
        setMegaOpen(false);
        setMobileOpen(false);
      }
    };
    document.addEventListener("pointerdown", closeMenu);
    document.addEventListener("keydown", closeOnEscape);
    return () => {
      document.removeEventListener("pointerdown", closeMenu);
      document.removeEventListener("keydown", closeOnEscape);
    };
  }, []);

  const closeMobile = () => {
    setMobileOpen(false);
    setMobileProducts(false);
    setExpandedMobileCategory(null);
  };

  const isProductsActive = currentPath.startsWith("/category") || currentPath.startsWith("/product");

  return (
    <header className="fixed inset-x-0 top-0 z-50">
      {/* Top Contact Bar */}
      <div className="hidden border-b border-white/10 bg-slate-950 text-slate-300 lg:block">
        <div className="container-shell flex h-10 items-center justify-between text-xs font-medium">
          <span>Automated construction material production systems</span>
          <div className="flex items-center gap-6">
            <a className="flex items-center gap-2 hover:text-gold-500" href="tel:+919876543210">
              <Phone size={13} /> +91 98765 43210
            </a>
            <a className="flex items-center gap-2 hover:text-gold-500" href="mailto:hello@ultra-tiles.com">
              <Mail size={13} /> hello@ultra-tiles.com
            </a>
            <div className="flex items-center gap-4 border-l border-white/10 pl-6">
              <a href="#" className="hover:text-gold-500 transition-colors" aria-label="Facebook"><Facebook size={14} /></a>
              <a href="#" className="hover:text-gold-500 transition-colors" aria-label="Twitter"><Twitter size={14} /></a>
              <a href="#" className="hover:text-gold-500 transition-colors" aria-label="Instagram"><Instagram size={14} /></a>
              <a href="#" className="hover:text-gold-500 transition-colors" aria-label="LinkedIn"><Linkedin size={14} /></a>
              <a href="#" className="hover:text-gold-500 transition-colors" aria-label="YouTube"><Youtube size={14} /></a>
            </div>
          </div>
        </div>
      </div>

      {/* Main Navbar */}
      <nav className="border-b border-white/10 bg-[#081422]/94 shadow-2xl shadow-slate-950/10 backdrop-blur-xl">
        <div className="container-shell flex h-[110px] items-center justify-between">
          <Logo />

          {/* Desktop Nav Links */}
          <div className="hidden items-center gap-1 lg:flex">
            <Link
              className={`nav-link ${currentPath === "/" ? "nav-active" : ""}`}
              to="/"
            >
              Home
            </Link>

            {/* Products Mega-Menu Link */}
            <div
              className="relative"
              ref={desktopMenuRef}
              onMouseEnter={() => setMegaOpen(true)}
              onMouseLeave={() => setMegaOpen(false)}
            >
              <button
                type="button"
                className={`nav-link flex items-center gap-1 ${isProductsActive ? "nav-active" : ""
                  }`}
                aria-expanded={megaOpen}
                aria-controls="products-mega-menu"
                onClick={() => setMegaOpen((open) => !open)}
              >
                Products
                <ChevronDown
                  size={15}
                  className={`transition ${megaOpen ? "rotate-180" : ""}`}
                />
              </button>

              {/* Mega Dropdown Panel */}
              <div
                id="products-mega-menu"
                className={`absolute left-1/2 top-full w-[900px] -translate-x-[45%] overflow-hidden rounded-3xl border border-white/10 bg-slate-950 p-3 shadow-2xl shadow-slate-950/40 transition duration-200 ${megaOpen
                    ? "visible translate-y-0 opacity-100"
                    : "invisible -translate-y-2 opacity-0"
                  }`}
              >
                <div className="grid grid-cols-[380px_1fr] gap-3">
                  {/* Left Column: Categories List */}
                  <div className="flex flex-col gap-1 p-2 border-r border-white/5 max-h-[420px] overflow-y-auto">
                    <p className="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-500 text-left">
                      Product Categories
                    </p>
                    {productCategories.map((category, index) => (
                      <Link
                        key={category.id}
                        to={`/category/${category.id}`}
                        onMouseEnter={() => setHoveredCategoryIndex(index)}
                        className={`group flex items-center gap-3 rounded-xl p-2 text-left transition ${hoveredCategoryIndex === index
                            ? "bg-white/[0.08] text-white"
                            : "text-slate-400 hover:bg-white/[0.03] hover:text-white"
                          }`}
                        onClick={() => setMegaOpen(false)}
                      >
                        <img
                          src={category.image}
                          alt=""
                          className="h-10 w-12 rounded-lg object-cover bg-white border border-white/10 shrink-0"
                        />
                        <div className="overflow-hidden">
                          <p className="text-xs font-bold leading-snug truncate">{category.name}</p>
                          <p className="text-[10px] text-slate-500 truncate mt-0.5">{category.description}</p>
                        </div>
                      </Link>
                    ))}
                  </div>

                  {/* Right Column: Sub-menu items for Hovered Category */}
                  {(() => {
                    const hoveredCat = productCategories[hoveredCategoryIndex];
                    if (!hoveredCat) return null;
                    const hoveredSubItems = hoveredCat.subItems || hoveredCat.subCategories || [];
                    return (
                      <div className="flex flex-col p-4 bg-slate-900/40 rounded-2xl max-h-[420px] overflow-y-auto justify-between">
                        <div>
                          <div className="flex items-center justify-between pb-3 border-b border-white/5">
                            <span className="text-[11px] font-extrabold uppercase tracking-[0.18em] text-orange-500">
                              Sub Categories
                            </span>
                            <Link
                              to={`/category/${hoveredCat.id}`}
                              onClick={() => setMegaOpen(false)}
                              className="text-[11px] font-bold text-slate-400 hover:text-white flex items-center gap-1"
                            >
                              Explore {hoveredCat.name} <ArrowRight size={12} />
                            </Link>
                          </div>
                          <div className="mt-3 space-y-2">
                            {hoveredSubItems.length ? hoveredSubItems.map((subItem) => (
                              <Link
                                key={subItem.id}
                                to={`/product/${subItem.id}`}
                                onClick={() => setMegaOpen(false)}
                                className="group flex gap-3 rounded-xl p-2 text-left transition hover:bg-white/[0.05]"
                              >
                                <img
                                  src={subItem.image}
                                  alt=""
                                  className="h-12 w-16 rounded-lg object-cover bg-white border border-white/10 shrink-0"
                                />
                                <div className="overflow-hidden flex flex-col justify-center">
                                  <h4 className="text-xs font-bold text-slate-200 group-hover:text-orange-400 transition leading-tight truncate">
                                    {subItem.name}
                                  </h4>
                                  <p className="mt-1 text-[10px] text-slate-400 line-clamp-2 leading-relaxed">
                                    {subItem.description}
                                  </p>
                                </div>
                              </Link>
                            )) : (
                              <div className="rounded-xl border border-dashed border-white/10 bg-white/[0.03] px-4 py-5 text-center text-[11px] text-slate-500">
                                No sub categories yet. Add them from the admin panel.
                              </div>
                            )}
                          </div>
                        </div>
                      </div>
                    );
                  })()}
                </div>
              </div>
            </div>

            {/* Other Navbar Links */}
            {links.slice(1).map((link) => {
              const isActive = currentPath === link.href;
              return (
                <Link
                  key={link.label}
                  className={`nav-link ${isActive ? "nav-active" : ""}`}
                  to={link.href}
                >
                  {link.label}
                </Link>
              );
            })}
          </div>

          <Link className="button-primary hidden xl:inline-flex text-[11px] uppercase tracking-wider px-5 py-3" to="/contact">
            Request a Quote <ArrowRight size={14} />
          </Link>

          {/* Mobile Menu Toggle Button */}
          <button
            type="button"
            className="grid h-11 w-11 place-items-center rounded-xl border border-white/15 text-white lg:hidden"
            aria-expanded={mobileOpen}
            aria-label={mobileOpen ? "Close navigation menu" : "Open navigation menu"}
            onClick={() => setMobileOpen((open) => !open)}
          >
            {mobileOpen ? <X /> : <Menu />}
          </button>
        </div>
      </nav>

      {/* Mobile Menu Panel */}
      <div
        className={`fixed inset-x-0 top-[77px] h-[calc(100vh-77px)] overflow-y-auto bg-slate-950 px-5 pb-8 pt-5 transition duration-300 lg:hidden ${mobileOpen ? "visible translate-x-0 opacity-100" : "invisible translate-x-full opacity-0"
          }`}
      >
        <div className="mx-auto max-w-md">
          <Link className="mobile-link" to="/" onClick={closeMobile}>
            Home
          </Link>

          {/* Mobile Products Drawer */}
          <button
            type="button"
            className="mobile-link w-full justify-between"
            aria-expanded={mobileProducts}
            onClick={() => setMobileProducts((open) => !open)}
          >
            Products
            <ChevronDown className={mobileProducts ? "rotate-180 transition" : "transition"} size={18} />
          </button>

          {mobileProducts && (
            <div className="mb-3 grid gap-1.5 rounded-2xl bg-white/[0.025] p-3 text-left">
              {productCategories.map((category) => {
                const isCatExpanded = expandedMobileCategory === category.id;
                return (
                  <div key={category.id} className="border-b border-white/[0.04] pb-2 last:border-0 last:pb-0">
                    <div className="flex items-center justify-between">
                      <Link
                        className="flex items-center gap-2.5 py-2 text-sm font-semibold text-slate-200"
                        to={`/category/${category.id}`}
                        onClick={closeMobile}
                      >
                        <img
                          src={category.image}
                          alt=""
                          className="h-8 w-10 rounded object-cover bg-white border border-white/10"
                        />
                        <span>{category.name}</span>
                      </Link>
                      <button
                        type="button"
                        onClick={() => setExpandedMobileCategory(isCatExpanded ? null : category.id)}
                        className="p-2 text-slate-400 hover:text-white"
                      >
                        <ChevronDown className={`h-4 w-4 transition ${isCatExpanded ? "rotate-180" : ""}`} />
                      </button>
                    </div>
                    {/* Expandable sub-items for mobile */}
                    {isCatExpanded && (
                      <div className="mt-1 pl-12 space-y-2">
                        {(category.subItems || category.subCategories || []).length ? (category.subItems || category.subCategories || []).map((sub) => (
                          <Link
                            key={sub.id}
                            to={`/product/${sub.id}`}
                            onClick={closeMobile}
                            className="flex items-center gap-2 text-xs font-medium text-slate-400 hover:text-white py-1"
                          >
                            <img
                              src={sub.image}
                              alt=""
                              className="h-6 w-8 rounded object-cover bg-white"
                            />
                            <span>{sub.name}</span>
                          </Link>
                        )) : (
                          <div className="rounded-xl border border-dashed border-white/10 px-3 py-3 text-[11px] text-slate-500">
                            No sub categories added yet.
                          </div>
                        )}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          )}

          {/* Other Links */}
          {links.slice(1).map((link) => (
            <Link
              className="mobile-link"
              key={link.label}
              to={link.href}
              onClick={closeMobile}
            >
              {link.label}
            </Link>
          ))}

          <Link
            className="button-primary mt-7 w-full justify-center text-xs uppercase tracking-wider"
            to="/contact"
            onClick={closeMobile}
          >
            Request a Quote <ArrowRight size={15} />
          </Link>

          <div className="mt-8 space-y-4 border-t border-white/10 pt-6 text-sm text-slate-400 text-left">
            <a className="flex items-center gap-3 hover:text-gold-500" href="tel:+919876543210">
              <Phone size={16} className="text-gold-500" /> +91 98765 43210
            </a>
            <a className="flex items-center gap-3 hover:text-gold-500" href="mailto:hello@ultra-tiles.com">
              <Mail size={16} className="text-gold-500" /> hello@ultra-tiles.com
            </a>
          </div>
        </div>
      </div>
    </header>
  );
}
