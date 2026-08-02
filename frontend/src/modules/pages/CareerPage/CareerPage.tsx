"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";
import { useCms, useCmsText } from "@/src/cms";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { GlassPanel } from "@/src/ui";

type Vacancy = {
  id: string;
  title: string;
  department: string;
  format: string;
  location: string;
  experience: string;
  salary: string;
  lead: string;
  tasks: string[];
  requirements: string[];
  perks: string[];
  image: string;
};

const vacancies: Vacancy[] = [
  {
    id: "interior-designer",
    title: "Дизайнер интерьера",
    department: "Интерьеры",
    format: "Гибрид / удаленно",
    location: "Самара или другой город",
    experience: "от 2 лет",
    salary: "от 90 000 ₽ + проектный бонус",
    lead: "Для жилых проектов: планировки, концепции, 3D-постановка задач и уверенная коммуникация с клиентом.",
    tasks: ["вести проект от брифа до рабочей документации", "собирать мудборды и концептуальные решения", "готовить ТЗ для визуализаторов и смежников"],
    requirements: ["портфолио реализованных или детально проработанных интерьеров", "понимание эргономики, материалов и узлов", "спокойная, точная коммуникация с клиентами"],
    perks: ["сильная команда визуализации", "понятные этапы и чек-листы", "проекты квартир и домов разного масштаба"],
    image: "/images/cms/warm-interior-before.webp",
  },
  {
    id: "3d-visualizer",
    title: "3D-визуализатор интерьеров",
    department: "3D production",
    format: "Удаленно",
    location: "Любой город",
    experience: "от 1,5 лет",
    salary: "сдельно, от 3 500 ₽ за ракурс",
    lead: "Нужен человек с аккуратным светом, материалами и вниманием к атмосфере, а не только к геометрии.",
    tasks: ["создавать фотореалистичные интерьерные ракурсы", "работать по ТЗ дизайнера и референсам", "готовить быстрые превью и финальные рендеры"],
    requirements: ["3ds Max + Corona/V-Ray или сопоставимый стек", "чистые сцены и понятная организация файлов", "вкус к натуральным материалам, свету и композиции"],
    perks: ["регулярный поток задач", "удаленная работа без лишних созвонов", "референсы и ТЗ без хаоса"],
    image: "/images/cms/warm-interior-after.webp",
  },
  {
    id: "architect",
    title: "Архитектор-проектировщик",
    department: "Архитектура",
    format: "Проектная занятость",
    location: "Самара / удаленно",
    experience: "от 3 лет",
    salary: "обсуждается по проекту",
    lead: "Для коттеджей и малых коммерческих объектов: фасады, планировочная логика, рабочие решения.",
    tasks: ["разрабатывать планировочные и фасадные концепции", "готовить комплект чертежей для согласования", "координироваться с визуализаторами и дизайнерами"],
    requirements: ["уверенная работа с Archicad/Revit или AutoCAD", "понимание конструктивной и инженерной логики", "портфолио частных домов или коммерческих объектов"],
    perks: ["проектная загрузка без офисной рутины", "задачи на стыке архитектуры и визуализации", "адекватные сроки на проработку"],
    image: "/images/cms/villa-exterior.webp",
  },
  {
    id: "project-manager",
    title: "Менеджер дизайн-проектов",
    department: "Производство",
    format: "Гибрид",
    location: "Самара",
    experience: "от 2 лет",
    salary: "от 80 000 ₽",
    lead: "Человек, который держит сроки, документы и коммуникацию так, чтобы дизайнеры могли спокойно проектировать.",
    tasks: ["вести календарь проекта и контроль этапов", "собирать вводные от клиента и команды", "готовить статусы, счета и простую проектную документацию"],
    requirements: ["опыт в дизайне, ремонте, архитектуре или смежной сфере", "структурность и бережная настойчивость", "умение переводить хаос в понятный список действий"],
    perks: ["влияние на качество процесса", "команда без микроменеджмента", "рост в операционное управление студией"],
    image: "/images/cms/office-space.webp",
  },
  {
    id: "procurement-specialist",
    title: "Специалист по комплектации",
    department: "Комплектация",
    format: "Гибрид / частичная занятость",
    location: "Самара",
    experience: "от 1 года",
    salary: "фикс + бонус",
    lead: "Для подбора материалов, мебели, света и поставщиков без случайных решений и сорванных сроков.",
    tasks: ["подбирать позиции под концепцию и бюджет", "вести таблицы, аналоги и статусы заказов", "коммуницировать с салонами, поставщиками и подрядчиками"],
    requirements: ["знание рынка отделочных материалов и мебели", "внимание к артикулам, срокам и ценам", "умение предлагать аналоги без потери визуальной идеи"],
    perks: ["живые проекты, а не абстрактные подборки", "контакт с дизайнерами и поставщиками", "гибкий формат занятости"],
    image: "/images/cms/country-house-interior.webp",
  },
];

const inputCls =
  "w-full rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-[#F5F2EC] outline-none transition placeholder:text-white/25 focus:border-[#D69A66]/60 focus:bg-white/[0.07]";

function ApplyModal({ vacancy, onClose }: { vacancy: Vacancy | null; onClose: () => void }) {
  const text = useCmsText();
  const backdropRef = useRef<HTMLDivElement>(null);
  const [name, setName] = useState("");
  const [contact, setContact] = useState("");
  const [portfolio, setPortfolio] = useState("");
  const [message, setMessage] = useState("");
  const [agreed, setAgreed] = useState(true);
  const [sent, setSent] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!vacancy) return;
    const onKey = (event: KeyboardEvent) => event.key === "Escape" && onClose();
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    return () => {
      document.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
    };
  }, [vacancy, onClose]);

  useEffect(() => {
    if (!vacancy) return;
    setSent(false);
    setError("");
  }, [vacancy]);

  if (!vacancy) return null;

  const handleSubmit = () => {
    if (!name.trim() || !contact.trim()) {
      setError(text("career.modal.requiredError", "Заполните имя и контакт, чтобы мы могли ответить."));
      return;
    }

    if (!agreed) {
      setError(text("career.modal.consentError", "Нужно согласие на обработку данных."));
      return;
    }

    const payload = {
      source: "career-page",
      vacancyId: vacancy.id,
      vacancyTitle: vacancy.title,
      name: name.trim(),
      contact: contact.trim(),
      portfolio: portfolio.trim(),
      message: message.trim(),
      createdAt: new Date().toISOString(),
    };

    localStorage.setItem("careerApplication", JSON.stringify(payload));
    setSent(true);
    setError("");
  };

  return (
    <div
      ref={backdropRef}
      onClick={(event) => event.target === backdropRef.current && onClose()}
      className="fixed inset-0 z-[100] flex items-end justify-center p-4 sm:items-center"
      style={{ background: "rgba(5,5,5,0.85)", backdropFilter: "blur(14px)" }}
    >
      <div className="relative w-full max-w-2xl overflow-hidden rounded-[2rem] border border-white/10 bg-[#111111] shadow-[0_40px_120px_rgba(0,0,0,0.75)]">
        <div className="pointer-events-none absolute -top-28 right-0 h-64 w-64 rounded-full bg-[#D69A66]/10 blur-3xl" />

        <div className="relative px-6 pb-4 pt-6 md:px-8">
          <button
            onClick={onClose}
            aria-label={text("career.modal.closeAria", "Закрыть")}
            className="absolute right-5 top-5 flex h-8 w-8 items-center justify-center rounded-full border border-white/10 text-white/35 transition hover:border-white/25 hover:text-white"
          >
            ×
          </button>
          <p className="mb-1 text-[10px] uppercase tracking-[0.4em] text-[#D69A66]">{text("career.modal.label", "Отклик на вакансию")}</p>
          <h2 className="pr-10 text-3xl font-light tracking-[-0.04em] text-[#F5F2EC]">{vacancy.title}</h2>
          <p className="mt-2 text-sm text-[#D6D1CA]">{vacancy.department} · {vacancy.format}</p>
        </div>

        <div className="mx-6 border-t border-white/8 md:mx-8" />

        <div className="grid gap-3 px-6 py-5 md:px-8">
          <div className="grid gap-3 md:grid-cols-2">
            <input className={inputCls} placeholder={text("career.modal.namePlaceholder", "Имя и фамилия")} value={name} onChange={(event) => setName(event.target.value)} />
            <input className={inputCls} placeholder={text("career.modal.contactPlaceholder", "Телефон, e-mail или Telegram")} value={contact} onChange={(event) => setContact(event.target.value)} />
          </div>
          <input className={inputCls} placeholder={text("career.modal.portfolioPlaceholder", "Ссылка на портфолио / резюме")} value={portfolio} onChange={(event) => setPortfolio(event.target.value)} />
          <textarea
            className={`${inputCls} min-h-32 resize-none`}
            placeholder={text("career.modal.messagePlaceholder", "Коротко о себе и релевантном опыте")}
            value={message}
            onChange={(event) => setMessage(event.target.value)}
          />

          <label className="flex cursor-pointer items-start gap-3 pt-1">
            <input type="checkbox" checked={agreed} onChange={(event) => setAgreed(event.target.checked)} className="mt-1 accent-[#D69A66]" />
            <span className="text-xs leading-relaxed text-white/40">
              {text("career.modal.consentStart", "Я согласен(-на) с")}{" "}
              <Link href="/politika-konfidencialnosti" target="_blank" className="text-[#D69A66]/70 underline underline-offset-2">
                {text("career.modal.privacy", "политикой конфиденциальности")}
              </Link>{" "}
              {text("career.modal.consentEnd", "и передачей данных для рассмотрения отклика.")}
            </span>
          </label>

          {error && <p className="rounded-2xl border border-[#D69A66]/25 bg-[#D69A66]/10 px-4 py-3 text-sm text-[#F5F2EC]">{error}</p>}
          {sent && (
            <p className="rounded-2xl border border-[#D69A66]/25 bg-[#D69A66]/10 px-4 py-3 text-sm text-[#F5F2EC]">
              {text("career.modal.sentMessage", "Отклик сохранен. Мы свяжемся с вами после рассмотрения заявки.")}
            </p>
          )}

          <button
            type="button"
            onClick={handleSubmit}
            className="h-14 rounded-full bg-[#D69A66] px-7 text-sm font-medium uppercase tracking-[0.24em] text-[#050505] transition hover:bg-[#F5F2EC]"
          >
            {text("career.modal.submitButton", "Отправить отклик")}
          </button>
        </div>
      </div>
    </div>
  );
}

function VacancyCard({
  vacancy,
  onApply,
  allVacancies,
}: {
  vacancy: Vacancy;
  onApply: (vacancy: Vacancy) => void;
  allVacancies: Vacancy[];
}) {
  const text = useCmsText();
  const currentIndex = Math.max(allVacancies.findIndex((item) => item.id === vacancy.id), 0);

  return (
    <GlassPanel className="group overflow-hidden rounded-[2rem] transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/55 hover:shadow-[0_24px_90px_rgba(0,0,0,0.38)]">
      <div className="grid lg:grid-cols-[0.72fr_1fr]">
        <div className="relative min-h-72 overflow-hidden">
          <CinematicImage
            frames={[
              vacancy.image,
              allVacancies[(currentIndex + 1) % allVacancies.length]?.image,
              allVacancies[(currentIndex + 2) % allVacancies.length]?.image,
            ]}
            alt={vacancy.title}
            fill
            hint="role"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/88 via-[#050505]/28 to-transparent" />
          <div className="absolute left-5 top-5 rounded-full border border-[#D69A66]/40 bg-[#050505]/55 px-3 py-1 text-[10px] uppercase tracking-[0.24em] text-[#D69A66] backdrop-blur">
            {vacancy.department}
          </div>
        </div>

        <div className="p-6 md:p-8">
          <div className="mb-5 flex flex-wrap gap-2 text-xs text-white/45">
            <span className="rounded-full border border-white/10 px-3 py-1">{vacancy.format}</span>
            <span className="rounded-full border border-white/10 px-3 py-1">{vacancy.location}</span>
            <span className="rounded-full border border-white/10 px-3 py-1">{vacancy.experience}</span>
          </div>
          <h2 className="text-4xl font-light leading-tight tracking-[-0.045em] text-[#F5F2EC]">{vacancy.title}</h2>
          <p className="mt-3 text-lg text-[#D69A66]">{vacancy.salary}</p>
          <p className="mt-5 max-w-2xl leading-relaxed text-[#D6D1CA]">{vacancy.lead}</p>

          <div className="mt-7 grid gap-6 md:grid-cols-2">
            <div>
              <p className="mb-3 text-[10px] uppercase tracking-[0.3em] text-white/35">{text("career.card.tasksLabel", "Задачи")}</p>
              <ul className="space-y-2">
                {vacancy.tasks.map((item) => (
                  <li key={item} className="flex gap-3 text-sm leading-relaxed text-white/55">
                    <span className="mt-2 h-1 w-1 shrink-0 rounded-full bg-[#D69A66]" />
                    {item}
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <p className="mb-3 text-[10px] uppercase tracking-[0.3em] text-white/35">{text("career.card.requirementsLabel", "Важно")}</p>
              <ul className="space-y-2">
                {vacancy.requirements.map((item) => (
                  <li key={item} className="flex gap-3 text-sm leading-relaxed text-white/55">
                    <span className="mt-2 h-1 w-1 shrink-0 rounded-full bg-[#D69A66]" />
                    {item}
                  </li>
                ))}
              </ul>
            </div>
          </div>

          <div className="mt-7 flex flex-wrap gap-2">
            {vacancy.perks.map((perk) => (
              <span key={perk} className="rounded-full bg-white/[0.05] px-3 py-1.5 text-xs text-white/45">
                {perk}
              </span>
            ))}
          </div>

          <button
            type="button"
            onClick={() => onApply(vacancy)}
            className="mt-8 inline-flex items-center gap-3 rounded-full border border-[#D69A66]/50 px-7 py-4 text-xs uppercase tracking-[0.24em] text-[#D69A66] transition hover:bg-[#D69A66] hover:text-[#050505]"
          >
            {text("career.card.applyButton", "Откликнуться")}
            <span>→</span>
          </button>
        </div>
      </div>
    </GlassPanel>
  );
}

function normalizeCmsVacancy(item: any, index: number): Vacancy {
  return {
    id: String(item?.id ?? item?.slug ?? `cms-vacancy-${index}`),
    title: String(item?.title ?? ""),
    department: String(item?.department ?? item?.employment ?? "Команда"),
    format: String(item?.format ?? item?.employment ?? ""),
    location: String(item?.location ?? ""),
    experience: String(item?.experience ?? ""),
    salary: String(item?.salary ?? ""),
    lead: String(item?.lead ?? item?.description ?? ""),
    tasks: Array.isArray(item?.tasks) ? item.tasks : Array.isArray(item?.responsibilities) ? item.responsibilities : [],
    requirements: Array.isArray(item?.requirements) ? item.requirements : [],
    perks: Array.isArray(item?.perks) ? item.perks : [],
    image: String(item?.image ?? vacancies[index % vacancies.length]?.image ?? "/images/cms/office-space.webp"),
  };
}

export default function CareerPage() {
  const { careerVacancies } = useCms();
  const text = useCmsText();
  const pageVacancies = useMemo(() => {
    const cmsItems = careerVacancies.map(normalizeCmsVacancy).filter((item) => item.title);
    return cmsItems.length ? cmsItems : vacancies;
  }, [careerVacancies]);
  const [activeVacancy, setActiveVacancy] = useState<Vacancy | null>(null);
  const departments = useMemo(
    () =>
      Array.from(
        new Set([
          ...pageVacancies.map((vacancy) => vacancy.department).filter(Boolean),
          text("career.departmentFallback1", "Интерьеры"),
          text("career.departmentFallback2", "3D-визуализация"),
          text("career.departmentFallback3", "Комплектация"),
          text("career.departmentFallback4", "Архитектура"),
          text("career.departmentFallback5", "Ландшафт"),
        ]),
      ).slice(0, 7),
    [pageVacancies, text],
  );

  return (
    <>
      <div className="page-in">
        <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
          <HeroBackdropSlider
            slides={[
              {
                image: text("career.hero.image", "/images/cms/career-team.webp"),
                alt: text("career.hero.imageAlt", "Команда дизайн-студии за рабочим столом"),
              },
              { image: pageVacancies[0]?.image, alt: pageVacancies[0]?.title },
              { image: pageVacancies[1]?.image, alt: pageVacancies[1]?.title },
            ]}
          />
          <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.74)_48%,rgba(5,5,5,.24)_100%)]" />
          <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.42)_34%,transparent_78%)]" />

          <div className="relative z-10 mx-auto grid min-h-[calc(100vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
            <div className="pb-8">
              <p className="text-xs uppercase tracking-[0.38em] text-[#D69A66]">{text("career.hero.label", "Карьера / 3D Smart Design Studio")}</p>
              <h1 className="mt-5 max-w-5xl text-[clamp(3rem,6.4vw,6.2rem)] font-light leading-[0.94] tracking-[-0.045em] text-white">
                {text("career.hero.title", "Карьера в студии")}
              </h1>
              <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
                {text("career.hero.text", "Ищем людей, которые умеют делать пространство понятным: в концепции, визуализации, документации, комплектации и коммуникации.")}
              </p>
              <div className="mt-9 flex flex-wrap gap-3">
                <a
                  href="#vacancies"
                  className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
                >
                  {text("career.hero.primaryButton", "Смотреть вакансии")}
                </a>
                <button
                  type="button"
                  onClick={() => setActiveVacancy(pageVacancies[0] ?? null)}
                  className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white"
                >
                  {text("career.hero.secondaryButton", "Отправить резюме")}
                </button>
              </div>
            </div>

            <div className="mb-8 grid gap-4">
              <div className="grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 sm:grid-cols-3">
                {[
                  [text("career.hero.stat1.value", "5"), text("career.hero.stat1.label", "открытых направлений")],
                  [text("career.hero.stat2.value", "гибрид"), text("career.hero.stat2.label", "и удаленная работа")],
                  [text("career.hero.stat3.value", "проектно"), text("career.hero.stat3.label", "можно начать без full-time")],
                ].map(([value, label]) => (
                  <GlassPanel key={value} className="p-5">
                    <strong className="block text-3xl font-light tracking-[-0.04em] text-[#D69A66]">{value}</strong>
                    <span className="mt-3 block text-xs uppercase leading-relaxed tracking-[0.18em] text-[#D6D1CA]">{label}</span>
                  </GlassPanel>
                ))}
              </div>

              <GlassPanel className="rounded-[2rem] p-6">
                <p className="text-xs uppercase tracking-[0.28em] text-[#D69A66]">{text("career.departments.label", "Кого ждем")}</p>
                <div className="mt-4 flex flex-wrap gap-2">
                  {departments.map((department) => (
                    <span key={department} className="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-sm text-white/60">
                      {department}
                    </span>
                  ))}
                </div>
              </GlassPanel>
            </div>
          </div>
        </section>

        <section id="vacancies" className="px-5 py-24 md:px-10 lg:px-16">
          <div className="mx-auto max-w-7xl">
            <div className="mb-14 grid gap-8 md:grid-cols-[1fr_0.7fr] md:items-end">
              <div>
                <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("career.vacancies.label", "Открытые позиции")}</p>
                <h2 className="max-w-4xl text-5xl font-light leading-[0.95] tracking-[-0.055em] md:text-7xl">
                  {text("career.vacancies.title", "Вакансии для тех, кто любит точность и красивый результат")}
                </h2>
              </div>
              <p className="text-lg leading-relaxed text-[#D6D1CA]">
                {text("career.vacancies.text", "Мы открыты к сотрудничеству с дизайнерами, визуализаторами, архитекторами и специалистами по комплектации. Отклики помогают собрать сильную проектную команду под новые задачи.")}
              </p>
            </div>

            <div className="grid gap-5">
              {pageVacancies.map((vacancy) => (
                <VacancyCard key={vacancy.id} vacancy={vacancy} onApply={setActiveVacancy} allVacancies={pageVacancies} />
              ))}
            </div>
          </div>
        </section>

        <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
          <div className="mx-auto max-w-7xl">
            <GlassPanel className="overflow-hidden rounded-[2.5rem] p-8 md:p-12">
              <div className="grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
                <div>
                  <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("career.cta.label", "Не нашли роль?")}</p>
                  <h2 className="max-w-3xl text-5xl font-light leading-tight tracking-[-0.055em] md:text-7xl">
                    {text("career.cta.title", "Напишите нам, если чувствуете совпадение")}
                  </h2>
                </div>
                <div>
                  <p className="text-lg leading-relaxed text-[#D6D1CA]">
                    {text("career.cta.text", "Иногда нужный специалист появляется раньше вакансии. Расскажите, чем можете усилить студию, и приложите портфолио.")}
                  </p>
                  <button
                    type="button"
                    onClick={() => setActiveVacancy(pageVacancies[0] ?? null)}
                    className="mt-8 inline-flex items-center gap-3 rounded-full bg-[#D69A66] px-8 py-4 text-sm uppercase tracking-[0.24em] text-[#050505] transition hover:bg-[#F5F2EC]"
                  >
                    {text("career.cta.button", "Отправить отклик")}
                    <span>→</span>
                  </button>
                </div>
              </div>
            </GlassPanel>
          </div>
        </section>
      </div>

      <ApplyModal vacancy={activeVacancy} onClose={() => setActiveVacancy(null)} />
    </>
  );
}
