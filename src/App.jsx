import { useEffect } from "react";
import { Routes, Route, useLocation } from "react-router-dom";
import Footer from "./components/Footer";
import Navbar from "./components/Navbar";
import ScrollToTop from "./components/ScrollToTop";
import Home from "./pages/Home";
import SolutionsPage from "./pages/SolutionsPage";
import ServicesPage from "./pages/ServicesPage";
import AboutPage from "./pages/AboutPage";
import ProjectsPage from "./pages/ProjectsPage";
import NewsPage from "./pages/NewsPage";
import ContactPage from "./pages/ContactPage";
import CategoryPage from "./pages/CategoryPage";
import ProductPage from "./pages/ProductPage";

function getBackendBaseUrl() {
  if (typeof window === "undefined") {
    return "http://localhost/ULTRATECH";
  }

  return `${window.location.protocol}//${window.location.hostname}/ULTRATECH`;
}

function BackendAdminRedirect() {
  const location = useLocation();

  useEffect(() => {
    const nextPath =
      location.pathname === "/backend/admin"
        ? "/backend/admin/"
        : location.pathname;

    window.location.replace(`${getBackendBaseUrl()}${nextPath}${location.search}${location.hash}`);
  }, [location.hash, location.pathname, location.search]);

  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-950 px-6 text-center text-white">
      <div className="max-w-lg">
        <p className="text-xs font-bold uppercase tracking-[0.3em] text-gold-500">
          Redirecting to admin panel
        </p>
        <h1 className="mt-4 text-2xl font-semibold sm:text-3xl">
          Opening the PHP backend admin
        </h1>
        <p className="mt-3 text-sm leading-6 text-slate-400">
          This route belongs to the backend app, so we are sending you to the correct Apache URL
          where the login page and admin assets load properly.
        </p>
      </div>
    </main>
  );
}

export default function App() {
  const location = useLocation();

  if (location.pathname === "/backend/admin" || location.pathname.startsWith("/backend/admin/")) {
    return <BackendAdminRedirect />;
  }

  return (
    <>
      <ScrollToTop />
      <Navbar />
      <main>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/solutions" element={<SolutionsPage />} />
          <Route path="/services" element={<ServicesPage />} />
          <Route path="/about" element={<AboutPage />} />
          <Route path="/projects" element={<ProjectsPage />} />
          <Route path="/news" element={<NewsPage />} />
          <Route path="/contact" element={<ContactPage />} />
          <Route path="/category/:categoryId" element={<CategoryPage />} />
          <Route path="/product/:productId" element={<ProductPage />} />
        </Routes>
      </main>
      <Footer />
    </>
  );
}
