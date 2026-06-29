import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { Check, CheckCircle2, ArrowRight, ArrowLeft, Shield, X, FileText } from 'lucide-react';
import { fetchProduct, submitContact } from '../api';
import { buildProductDetailSheet, cleanText } from '../data/productDetailSheet';
import { CardImage } from '../components/UI';

export default function ProductPage() {
  const { productId } = useParams();
  const [productObj, setProductObj] = useState(null);
  const [categoryObj, setCategoryObj] = useState(null);
  const [loading, setLoading] = useState(true);
  const [submitted, setSubmitted] = useState(false);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalLoading, setModalLoading] = useState(false);
  const detailSheet = productObj ? buildProductDetailSheet(productObj) : null;

  useEffect(() => {
    if (isModalOpen) document.body.style.overflow = 'hidden';
    else document.body.style.overflow = 'unset';
    return () => { document.body.style.overflow = 'unset'; };
  }, [isModalOpen]);

  useEffect(() => {
    setLoading(true);
    fetchProduct(productId).then((data) => {
      if (data && !data.error) {
        setProductObj(data);
        setCategoryObj(data.category || null);
      }
      setLoading(false);
    });
  }, [productId]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    setModalLoading(true);
    const fd = new FormData(form);
    const data = Object.fromEntries(fd.entries());
    data.product = cleanText(productObj?.name || '');
    await submitContact(data);
    form.reset();
    setSubmitted(true);
    setModalLoading(false);
  };

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-slate-950">
        <div style={{
          width: 44, height: 44, border: '3px solid #1e293b',
          borderTop: '3px solid #d4af37', borderRadius: '50%',
          animation: 'spin 0.8s linear infinite'
        }} />
        <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
      </div>
    );
  }

  if (!productObj) {
    return (
      <div className="bg-slate-50 pt-[116px] pb-24 text-center">
        <div className="container-shell py-20">
          <h1 className="font-display text-3xl font-bold text-slate-900">Product Not Found</h1>
          <p className="mt-4 text-slate-600">The requested machinery model does not exist.</p>
          <Link to="/" className="button-primary mt-8">Return Home</Link>
        </div>
      </div>
    );
  }

  return (
    <div className="overflow-hidden bg-slate-50 pt-[116px]">
      {/* Breadcrumb */}
      <section className="bg-slate-100 py-4 border-b border-slate-200">
        <div className="container-shell flex flex-col gap-3 text-xs font-semibold text-slate-500 sm:flex-row sm:items-center sm:justify-between">
          <div className="flex min-w-0 flex-wrap items-center gap-2">
            <Link to="/" className="hover:text-orange-500">Home</Link>
            <span>/</span>
            {categoryObj && (
              <>
                <Link to={`/category/${categoryObj.id}`} className="hover:text-orange-500">{cleanText(categoryObj.name)}</Link>
                <span>/</span>
              </>
            )}
            <span className="text-slate-800">{cleanText(productObj.name)}</span>
          </div>
          {categoryObj && (
            <Link to={`/category/${categoryObj.id}`} className="flex items-center gap-1.5 text-orange-600 hover:text-orange-700">
              <ArrowLeft size={13} /> Back to category
            </Link>
          )}
        </div>
      </section>

      {/* Product Banner */}
      <section className="relative min-h-[520px] overflow-hidden bg-[#071321] text-white sm:min-h-[560px] lg:min-h-[620px]">
        <div className="absolute inset-0 opacity-35">
          <img
            src={productObj.image}
            alt=""
            className="h-full w-full scale-110 object-cover object-center blur-2xl"
          />
        </div>
        <div className="absolute inset-0 flex items-start justify-center px-3 pt-6 sm:items-center sm:px-8 sm:pt-0 lg:justify-end lg:pr-[max(2.5rem,calc((100vw-1340px)/2+2.5rem))]">
          <img
            src={productObj.image}
            alt={cleanText(productObj.name)}
            className="max-h-[270px] w-full max-w-[520px] object-contain object-center opacity-85 drop-shadow-2xl sm:max-h-[390px] sm:max-w-[680px] lg:max-h-[520px] lg:w-[54vw] lg:max-w-[820px] lg:object-right"
          />
        </div>
        <div className="absolute inset-0 bg-gradient-to-t from-[#071321] via-[#071321]/74 to-[#071321]/18 sm:via-[#071321]/58 lg:from-[#071321] lg:via-[#071321]/38 lg:to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-r from-[#071321]/95 via-[#071321]/62 to-[#071321]/30 lg:via-[#071321]/36 lg:to-[#071321]/10" />
        <div className="container-shell relative z-10 flex min-h-[520px] flex-col justify-end pb-12 pt-10 sm:min-h-[560px] sm:pb-16 lg:min-h-[620px] lg:justify-center lg:pb-0">
          {categoryObj && (
            <Link
              to={`/category/${categoryObj.id}`}
              className="mb-6 inline-flex w-fit items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-orange-400 transition-colors hover:text-orange-300"
            >
              <ArrowLeft size={14} /> Back to {cleanText(categoryObj.name)}
            </Link>
          )}
          {categoryObj && (
            <span className="inline-block w-fit rounded-full border border-orange-500/20 bg-orange-500/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-orange-200">
              {cleanText(categoryObj.name)}
            </span>
          )}
          <h1 className="mt-4 max-w-4xl break-words font-display text-3xl font-extrabold tracking-tight sm:text-5xl lg:text-7xl">
            {cleanText(productObj.name)}
          </h1>
          <p className="mt-5 max-w-2xl text-sm leading-7 text-slate-300 sm:mt-6 sm:text-lg sm:leading-8">
            {cleanText(productObj.description)}
          </p>
          <div className="mt-7 flex flex-col gap-3 sm:flex-row">
            <button
              type="button"
              onClick={() => setIsModalOpen(true)}
              className="button-primary justify-center"
            >
              Request Quotation <ArrowRight size={18} />
            </button>
            <a href="#product-details" className="button-secondary justify-center">
              View Details <ArrowRight size={18} />
            </a>
          </div>
        </div>
      </section>

      {/* Main Showcase */}
      <section id="product-details" className="bg-white py-10 sm:py-14 lg:py-16">
        <div className="container-shell grid gap-8 md:gap-10 lg:grid-cols-[0.92fr_1.08fr] lg:items-start lg:gap-12">
          {/* Left: Image */}
          <div className="overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-gold-50 shadow-sm sm:rounded-3xl lg:sticky lg:top-[170px]">
            <CardImage
              src={productObj.image}
              alt={cleanText(productObj.name)}
              className="aspect-[1.12] h-full w-full sm:aspect-[4/3]"
              tone="light"
              padding="p-0"
              fit="contain"
              imageClassName="background-image object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
            />
            <div className="flex items-center gap-4 bg-[#071321] p-4 text-white sm:p-6">
              <Shield className="shrink-0 text-orange-500" size={26} />
              <div className="min-w-0 break-words">
                <p className="text-sm font-bold">12-Month Structural Warranty</p>
                <p className="text-xs leading-5 text-slate-400">Includes commissioning engineers and site repair coverage.</p>
              </div>
            </div>
          </div>

          {/* Right: Details */}
          <div className="min-w-0">
            {categoryObj && (
              <span className="inline-block rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-600">
                {cleanText(categoryObj.name)}
              </span>
            )}
            <h1 className="mt-4 font-display text-2xl font-bold leading-tight tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">
              {cleanText(productObj.name)}
            </h1>
            <p className="mt-5 break-words text-sm leading-7 text-slate-600 sm:mt-6 sm:text-base sm:leading-8">{cleanText(productObj.description)}</p>

            {/* Specs Table */}
            {productObj.specs && Object.keys(productObj.specs).length > 0 && (
              <div className="mt-8 overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                <div className="bg-slate-950 px-5 py-4 sm:px-6">
                  <h3 className="font-display text-xs font-bold uppercase tracking-wider text-white sm:text-sm">Technical Specifications Table</h3>
                </div>
                <div className="divide-y divide-slate-100 bg-white px-5 py-2 text-sm sm:px-6">
                  {Object.entries(productObj.specs).map(([key, val]) => (
                    <div key={key} className="grid gap-1 py-3.5 sm:grid-cols-[40%_60%] sm:gap-4">
                      <span className="text-slate-500 font-semibold">{cleanText(key)}</span>
                      <span className="break-words font-bold text-slate-900">{cleanText(val)}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Features */}
            {productObj.features?.length > 0 && (
              <div className="mt-8">
                <h3 className="font-display text-lg font-bold text-slate-950">Key Performance Advantages</h3>
                <div className="mt-4 grid gap-3 sm:grid-cols-2">
                  {productObj.features.map((feat) => (
                    <div key={feat} className="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3">
                      <Check className="text-orange-500 mt-0.5 shrink-0" size={17} />
                      <span className="text-xs font-semibold text-slate-700">{cleanText(feat)}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {detailSheet && (
              <div className="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-200/70 sm:mt-12 sm:rounded-[2rem]">
                <div className="border-b border-slate-100 bg-[#071321] px-5 py-6 text-white sm:px-8">
                  <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div className="min-w-0">
                      <p className="text-[10px] font-bold uppercase tracking-[0.18em] text-orange-300 sm:tracking-[0.3em]">{detailSheet.badge}</p>
                      <h2 className="mt-3 font-display text-xl font-bold sm:text-3xl">{detailSheet.title}</h2>
                      <p className="mt-3 max-w-3xl text-sm leading-7 text-slate-300">{detailSheet.summary}</p>
                    </div>
                    <div className="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                      <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Quotation Mode</p>
                      <p className="mt-1 text-sm font-semibold text-white">{detailSheet.totalLabel}</p>
                    </div>
                  </div>

                  {detailSheet.highlights?.length > 0 && (
                    <div className="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                      {detailSheet.highlights.map((item) => (
                        <div key={item.label} className="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                          <p className="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">{item.label}</p>
                          <p className="mt-1 text-sm font-semibold text-white">{item.value}</p>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {detailSheet.image && (
                  <div className="border-b border-slate-100 bg-slate-50 px-5 py-6 sm:px-8">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                      <div>
                        <p className="text-[10px] font-bold uppercase tracking-[0.28em] text-orange-500">Product Visual</p>
                        <p className="mt-2 text-sm leading-6 text-slate-500">
                          Same image used on the home page card, shown here in a full-width responsive preview.
                        </p>
                      </div>
                      <div className="hidden rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 shadow-sm sm:inline-flex">
                        Responsive preview
                      </div>
                    </div>
                    <div className="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:rounded-3xl">
                      <CardImage
                        src={detailSheet.image}
                        alt={cleanText(productObj.name)}
                        className="aspect-[4/3] h-full w-full sm:aspect-[16/9] lg:aspect-[18/8]"
                        tone="light"
                        padding="p-0"
                        fit="contain"
                        imageClassName="background-image object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
                      />
                    </div>
                  </div>
                )}

                <div className="hidden overflow-x-auto border-b border-slate-200 md:block">
                  <table className="min-w-[760px] w-full border-collapse bg-white text-left text-sm lg:min-w-[920px]">
                    <thead className="bg-slate-50 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                      <tr>
                        <th className="px-5 py-4">Items</th>
                        <th className="px-4 py-4">HSN</th>
                        <th className="px-4 py-4">Qty.</th>
                        <th className="px-4 py-4">Rate</th>
                        <th className="px-5 py-4 text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {detailSheet.lineItems.map((row, index) => (
                        <tr key={`${row.item}-${index}`} className={row.emphasis ? "bg-gold-50/70" : ""}>
                          <td className="px-5 py-4 align-top">
                            <p className={`font-semibold tracking-tight ${row.emphasis ? "text-slate-950" : "text-slate-900"}`}>{row.item}</p>
                            <p className="mt-1 text-xs leading-6 text-slate-500">{row.note}</p>
                          </td>
                          <td className="px-4 py-4 align-top text-sm font-semibold text-slate-600">{row.hsn}</td>
                          <td className="px-4 py-4 align-top text-sm font-semibold text-slate-600">{row.qty}</td>
                          <td className="px-4 py-4 align-top text-sm font-semibold text-slate-900">{row.rate}</td>
                          <td className="px-5 py-4 align-top text-right text-sm font-bold text-slate-950">{row.amount}</td>
                        </tr>
                      ))}
                    </tbody>
                    <tfoot className="border-t border-slate-200 bg-slate-50">
                      <tr>
                        <td className="px-5 py-4 text-right text-xs font-bold uppercase tracking-[0.18em] text-slate-500" colSpan={4}>
                          Indicative Total
                        </td>
                        <td className="px-5 py-4 text-right text-base font-black text-slate-950">{detailSheet.totalLabel}</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div className="divide-y divide-slate-100 border-b border-slate-200 bg-white md:hidden">
                  {detailSheet.lineItems.map((row, index) => (
                    <div key={`${row.item}-${index}`} className={`p-5 ${row.emphasis ? "bg-gold-50/70" : ""}`}>
                      <p className="font-semibold text-slate-950">{row.item}</p>
                      <p className="mt-2 text-xs leading-6 text-slate-500">{row.note}</p>
                      <div className="mt-4 grid grid-cols-2 gap-3 text-xs">
                        <div>
                          <span className="block font-bold uppercase tracking-wider text-slate-400">HSN</span>
                          <span className="mt-1 block font-semibold text-slate-700">{row.hsn}</span>
                        </div>
                        <div>
                          <span className="block font-bold uppercase tracking-wider text-slate-400">Qty.</span>
                          <span className="mt-1 block font-semibold text-slate-700">{row.qty}</span>
                        </div>
                        <div>
                          <span className="block font-bold uppercase tracking-wider text-slate-400">Rate</span>
                          <span className="mt-1 block font-semibold text-slate-900">{row.rate}</span>
                        </div>
                        <div>
                          <span className="block font-bold uppercase tracking-wider text-slate-400">Amount</span>
                          <span className="mt-1 block font-black text-slate-950">{row.amount}</span>
                        </div>
                      </div>
                    </div>
                  ))}
                  <div className="flex items-center justify-between gap-4 bg-slate-50 p-5">
                    <span className="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Indicative Total</span>
                    <span className="text-base font-black text-slate-950">{detailSheet.totalLabel}</span>
                  </div>
                </div>

                <div className="border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-8">
                  <p className="flex items-start gap-2 text-sm leading-6 text-slate-600">
                    <FileText className="mt-0.5 shrink-0 text-orange-500" size={16} />
                    <span>{detailSheet.note}</span>
                  </p>
                </div>
              </div>
            )}

            <div className="mt-10">
              <button onClick={() => setIsModalOpen(true)} className="button-primary w-full justify-center py-4 text-sm shadow-lg hover:shadow-orange-500/25 sm:text-lg">
                Request Quotation <ArrowRight size={20} />
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Inquiry Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 backdrop-blur-sm sm:p-4">
              <div className="relative my-6 w-full max-w-[650px] rounded-[2rem] bg-white p-5 shadow-2xl sm:my-16 sm:p-10">
            <button onClick={() => setIsModalOpen(false)} className="absolute top-6 right-6 p-2 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-900 transition" aria-label="Close modal">
              <X size={20} />
            </button>
            <div className="text-center mt-2">
              <p className="eyebrow">Request Pricing</p>
              <h2 className="mt-3 font-display text-2xl font-bold text-slate-950">Quotation for {cleanText(productObj.name)}</h2>
            </div>
            <form className="mt-8 space-y-5" onSubmit={handleSubmit}>
              <div className="grid gap-5 sm:grid-cols-2">
                <label className="form-field">Name * <input name="name" placeholder="Your name" required /></label>
                <label className="form-field">Email * <input name="email" type="email" placeholder="you@company.com" required /></label>
                <label className="form-field">Phone * <input name="phone" placeholder="+91" required /></label>
                <label className="form-field">Delivery Country * <input name="country" placeholder="e.g. India, UAE" required /></label>
              </div>
              <label className="form-field">
                Message / Customizations
                <textarea name="message" placeholder="Specify requirements..." rows="3" />
              </label>
              {submitted && (
                <p className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                  <CheckCircle2 size={18} /> Pricing request submitted. We will contact you shortly.
                </p>
              )}
              <button type="submit" className="button-primary w-full justify-center" disabled={modalLoading}>
                {modalLoading ? 'Submitting...' : <>Submit RFQ Request <ArrowRight size={18} /></>}
              </button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

