import { useState, useEffect } from "react";
import { CheckCircle2, MessageCircle, Mail, Phone, MapPin, ArrowRight } from "lucide-react";
import { fetchContactInfo, fetchProducts } from "../api";
import { products as fallbackProducts } from "../data/siteData";

export default function ContactPage() {
  const [submitted, setSubmitted] = useState(false);
  const [contactInfo, setContactInfo] = useState(null);
  const [products, setProducts] = useState([]);

  useEffect(() => {
    fetchContactInfo().then((data) => setContactInfo(data));
    fetchProducts().then((data) => setProducts(data?.products?.length ? data.products : fallbackProducts));
  }, []);

  const phone = contactInfo?.phone || "+91 98765 43210";
  const phoneHref = contactInfo?.phone_href || "tel:+919876543210";
  const email = contactInfo?.email || "hello@ultra-tiles.com";
  const whatsapp = contactInfo?.whatsapp || "https://wa.me/919876543210";
  const address = contactInfo?.address || "Industrial Growth Park, Pune, Maharashtra, India";

  const handleSubmit = (e) => {
    e.preventDefault();
    e.currentTarget.reset();
    setSubmitted(true);
  };

  return (
    <div className="bg-slate-50 pt-[116px]">
      {/* Header Banner */}
      <section className="relative overflow-hidden bg-[#071321] py-20 text-white sm:py-24">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(166,66,95,0.15),transparent)]" />
        <div className="container-shell relative z-10">
          <p className="eyebrow">Connect With Us</p>
          <h1 className="mt-4 font-display text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Contact <span className="text-gold-500">ULTRA Tile Machine</span>
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
            Ready to design your next block production line or require support? Get in touch with our engineering offices in India.
          </p>
        </div>
      </section>

      {/* Main Details and Form */}
      <section className="py-20">
        <div className="container-shell grid gap-12 lg:grid-cols-[.85fr_1.15fr]">
          {/* Info Side */}
          <div className="rounded-[2.5rem] bg-[#0a1727] p-8 text-white sm:p-12">
            <p className="eyebrow">Contact Details</p>
            <h2 className="mt-4 font-display text-3xl font-bold">
              Let's Discuss Specifications
            </h2>
            <p className="mt-4 text-sm leading-7 text-slate-300">
              Provide your details and we will put you in touch with an engineering consultant who can help map out aggregates, vibration forces, and capacity requirements.
            </p>

            <div className="mt-10 space-y-4">
              <a className="contact-action" href={whatsapp} target="_blank" rel="noopener noreferrer">
                <MessageCircle size={20} /> WhatsApp Us
              </a>
              <a className="contact-action" href={`mailto:${email}`}>
                <Mail size={20} /> {email}
              </a>
              <a className="contact-action" href={phoneHref}>
                <Phone size={20} /> {phone}
              </a>
            </div>

            <p className="mt-10 flex gap-3 border-t border-white/10 pt-8 text-sm leading-7 text-slate-400">
              <MapPin className="mt-0.5 shrink-0 text-gold-500" size={20} />
              {address}
            </p>
          </div>

          {/* Form Side */}
          <form className="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm sm:p-12" onSubmit={handleSubmit}>
            <h3 className="font-display text-2xl font-bold text-slate-950">
              Send an Inquiry
            </h3>
            <p className="mt-2 text-sm text-slate-500">
              Please fill out the form below. Fields marked * are required.
            </p>

            <div className="mt-8 grid gap-6 sm:grid-cols-2">
              <label className="form-field">
                Name *
                <input name="name" placeholder="Your full name" required />
              </label>
              <label className="form-field">
                Email *
                <input name="email" type="email" placeholder="you@company.com" required />
              </label>
              <label className="form-field">
                Phone Number *
                <input name="phone" type="tel" placeholder="+91" required />
              </label>
              <label className="form-field">
                Machinery Interest *
                <select name="product" defaultValue="" required>
                  <option disabled value="">Select machinery</option>
                  {products.map(p => (
                    <option key={p.id} value={p.title}>{p.title}</option>
                  ))}
                </select>
              </label>
            </div>
            <label className="form-field mt-6">
              Details of your project *
              <textarea
                name="message"
                placeholder="Capacity requirements, land dimension, raw aggregate materials (sand, fly ash, dust), and target budget..."
                rows="5"
                required
              />
            </label>

            {submitted && (
              <p className="mt-6 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                <CheckCircle2 size={18} /> Inquiry successfully recorded. Our engineering director will review and send you catalog brochures.
              </p>
            )}

            <button type="submit" className="button-primary mt-8 w-full justify-center">
              Submit Project Details <ArrowRight size={18} />
            </button>
          </form>
        </div>
      </section>
    </div>
  );
}

