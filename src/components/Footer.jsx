import { useState, useEffect } from "react";
import { Facebook, Instagram, Linkedin, MapPin, Mail, Phone, Youtube } from "lucide-react";
import { Link } from "react-router-dom";
import { fetchCategories, fetchContactInfo } from "../api";
import { getFallbackFooterCategories } from "../data/catalogData";
import LogoIcon from "./LogoIcon";

const quickLinks = [
  ["Home", "/"],
  ["Solutions", "/solutions"],
  ["Services", "/services"],
  ["About Us", "/about"],
  ["Resources", "/projects"],
  ["Contact", "/contact"],
];

const socials = [
  { label: "LinkedIn", icon: Linkedin },
  { label: "YouTube", icon: Youtube },
  { label: "Facebook", icon: Facebook },
  { label: "Instagram", icon: Instagram },
];

export default function Footer() {
  const [footerProducts, setFooterProducts] = useState(() =>
    getFallbackFooterCategories().map((cat) => ({ name: cat.name, href: `/category/${cat.id}` }))
  );
  const [contactInfo, setContactInfo] = useState(null);

  useEffect(() => {
    fetchCategories().then((cats) => {
      if (Array.isArray(cats)) {
        setFooterProducts(cats.slice(0, 6).map((cat) => ({ name: cat.name, href: `/category/${cat.id}` })));
      }
    });
    fetchContactInfo().then((info) => setContactInfo(info));
  }, []);

  const phone = contactInfo?.phone || "+91 98765 43210";
  const phoneHref = contactInfo?.phone_href || "tel:+919876543210";
  const email = contactInfo?.email === "hello@ultratech-machinery.com" ? "hello@ultra-tiles.com" : (contactInfo?.email || "hello@ultra-tiles.com");
  const address = contactInfo?.address || "Industrial Growth Park, Pune, Maharashtra, India";

  return (
    <footer className="border-t border-white/5 bg-navy-950 text-slate-400">
      <div className="container-shell grid gap-10 py-12 sm:grid-cols-2 sm:py-14 lg:grid-cols-[1.25fr_.8fr_1fr_1.1fr] lg:gap-12 lg:py-16">
        <div className="text-left sm:col-span-2 lg:col-span-1">
          <Link to="/" className="inline-flex items-center">
            <img src="/assets/logo.jpeg" alt="ULTRA Tile Machine Logo" className="h-20 w-auto rounded-lg object-contain shadow-sm sm:h-24" />
          </Link>
          <p className="mt-5 max-w-md text-sm leading-7 sm:mt-6 lg:max-w-sm">
            Intelligent block forming machinery and integrated material processing systems configured for plant efficiency and maximum output quality.
          </p>
          <div className="mt-7 flex flex-wrap gap-3">
            {socials.map(({ label, icon: Icon }) => (
              <a
                key={label}
                aria-label={label}
                href={contactInfo?.[label.toLowerCase()] || "#"}
                className="grid h-10 w-10 place-items-center rounded-lg border border-white/10 text-slate-300 transition hover:border-gold-500 hover:bg-gold-500 hover:text-navy-950"
              >
                <Icon size={18} />
              </a>
            ))}
          </div>
        </div>

        <div className="text-left">
          <p className="footer-title">Quick Links</p>
          <ul className="mt-5 space-y-3 text-left text-sm sm:mt-6 sm:space-y-3.5">
            {quickLinks.map(([label, href]) => (
              <li key={label}>
                <Link className="inline-flex py-0.5 transition hover:text-gold-500" to={href}>{label}</Link>
              </li>
            ))}
          </ul>
        </div>

        <div className="text-left">
          <p className="footer-title">Products</p>
          <ul className="mt-5 space-y-3 text-left text-sm sm:mt-6 sm:space-y-3.5">
            {footerProducts.length > 0 ? (
              footerProducts.map((item) => (
                <li key={item.name}>
                  <Link className="inline-flex py-0.5 transition hover:text-gold-500" to={item.href}>{item.name}</Link>
                </li>
              ))
            ) : (
              <>
                <li><Link className="transition hover:text-gold-500" to="/product/zenith-1500">Zenith 1500</Link></li>
                <li><Link className="transition hover:text-gold-500" to="/product/zenith-940">Zenith 940</Link></li>
                <li><Link className="transition hover:text-gold-500" to="/product/zenith-1200">Zenith 1200</Link></li>
                <li><Link className="transition hover:text-gold-500" to="/product/zenith-quantum-1200">Zenith Quantum 1200</Link></li>
              </>
            )}
          </ul>
        </div>

        <div className="text-left sm:col-span-2 lg:col-span-1">
          <p className="footer-title">Contact Info</p>
          <ul className="mt-5 space-y-5 text-sm leading-6 sm:mt-6">
            <li className="flex gap-3">
              <MapPin className="mt-1 shrink-0 text-gold-500" size={17} />
              <span className="min-w-0 break-words">{address}</span>
            </li>
            <li>
              <a className="flex gap-3 hover:text-gold-500" href={phoneHref}>
                <Phone className="shrink-0 text-gold-500" size={17} /> {phone}
              </a>
            </li>
            <li>
              <a className="flex gap-3 break-all hover:text-gold-500" href={`mailto:${email}`}>
                <Mail className="shrink-0 text-gold-500" size={17} />
                {email}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div className="border-t border-white/10 text-left">
        <div className="container-shell flex flex-col gap-4 py-6 text-xs sm:flex-row sm:items-center sm:justify-between">
          <p>&copy; 2026 ULTRA Tile Machine. All rights reserved.</p>
          <div className="flex flex-wrap gap-x-6 gap-y-2">
            <a href="#" className="hover:text-white">Privacy Policy</a>
            <a href="#" className="hover:text-white">Terms of Use</a>
          </div>
        </div>
      </div>
    </footer>
  );
}
