"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import { submitLead, useCms, useCmsText } from "@/src/cms";
import type { ServiceQuizKind } from "@/src/data";
import { GlassPanel } from "@/src/ui";
import SectionLabel from "@/src/components/common/SectionLabel";

type QuizOption = {
  label: string;
  note?: string;
  minArea?: number;
  maxArea?: number;
  pricePerMeter?: number;
  fixedPrice?: number;
  timeline?: string;
  visual?: "modern" | "classic" | "loft" | "eco" | "consult";
};

type QuizQuestion = {
  id: string;
  title: string;
  goal: string;
  options: QuizOption[];
};

type Answers = Record<string, QuizOption>;

const commonStartQuestion: QuizQuestion = {
  id: "start",
  title: "Когда планируете приступать к реализации?",
  goal: "Оценим приоритет и ближайшие свободные окна команды.",
  options: [
    { label: "Срочно", note: "Нужно было вчера" },
    { label: "В ближайший месяц" },
    { label: "В течение 3-6 месяцев" },
    { label: "Пока просто прицениваюсь" },
  ],
};

const quizQuestionSets: Record<ServiceQuizKind, QuizQuestion[]> = {
  interior: [
    {
      id: "object",
      title: "Тип объекта",
      goal: "Поможет понять масштаб и специфику проекта.",
      options: [
        {
          label: "Квартира в новостройке",
          note: "Чистый старт и гибкая планировка",
        },
        {
          label: "Квартира во вторичном жилье",
          note: "Учитываем демонтаж и существующие узлы",
        },
        {
          label: "Частный дом / коттедж",
          note: "Интерьер, архитектура и участок в одной логике",
        },
        {
          label: "Коммерческое помещение",
          note: "Офис, кафе, шоурум или другое пространство",
        },
        {
          label: "Только отдельная зона",
          note: "Кухня, гостиная, спальня или кабинет",
        },
      ],
    },
    {
      id: "area",
      title: "Площадь объекта",
      goal: "Нужна для первичного расчета стоимости.",
      options: [
        { label: "До 50 м²", minArea: 35, maxArea: 50 },
        { label: "50-100 м²", minArea: 50, maxArea: 100 },
        { label: "100-200 м²", minArea: 100, maxArea: 200 },
        { label: "Более 200 м²", minArea: 200, maxArea: 320 },
      ],
    },
    {
      id: "scope",
      title: "Какой объем услуг вам необходим?",
      goal: "Поймем, нужен полный цикл или отдельный этап.",
      options: [
        {
          label: "Полный дизайн-проект",
          note: "Чертежи, 3D-визуализация и комплектация",
          pricePerMeter: 1500,
          timeline: "8-14 недель",
        },
        {
          label: "Только 3D-визуализация",
          note: "Когда планировка и ТЗ уже готовы",
          fixedPrice: 45000,
          timeline: "от 5 рабочих дней",
        },
        {
          label: "Архитектурное проектирование",
          note: "Для строительства дома с нуля",
          pricePerMeter: 1800,
          timeline: "10-18 недель",
        },
        {
          label: "Удаленное проектирование",
          note: "Если объект находится в другом городе",
          pricePerMeter: 1300,
          timeline: "6-12 недель",
        },
      ],
    },
    {
      id: "style",
      title: "В каком стиле вы видите будущий интерьер?",
      goal: "Так дизайнер быстрее поймет визуальное направление.",
      options: [
        {
          label: "Современный",
          note: "Минимализм, контемпорари",
          visual: "modern",
        },
        {
          label: "Неоклассика",
          note: "Элегантность и мягкий уют",
          visual: "classic",
        },
        {
          label: "Лофт / индустриальный",
          note: "Фактура, металл, выразительная геометрия",
          visual: "loft",
        },
        {
          label: "Скандинавский / эко",
          note: "Свет, воздух, натуральные материалы",
          visual: "eco",
        },
        {
          label: "Пока не определился",
          note: "Нужна консультация дизайнера",
          visual: "consult",
        },
      ],
    },
    commonStartQuestion,
  ],
  architecture: [
    {
      id: "object",
      title: "Что нужно спроектировать?",
      goal: "Так мы поймем масштаб архитектурной задачи.",
      options: [
        { label: "Частный дом / коттедж", note: "Новый объект с нуля" },
        {
          label: "Реконструкция дома",
          note: "Работаем с существующим объемом",
        },
        {
          label: "Коммерческое здание",
          note: "Офис, павильон, общественное пространство",
        },
        { label: "Только фасады", note: "Нужно обновить внешний образ" },
      ],
    },
    {
      id: "area",
      title: "Ориентировочная площадь здания",
      goal: "Нужна для первичной оценки состава работ.",
      options: [
        { label: "До 150 м²", minArea: 80, maxArea: 150 },
        { label: "150-250 м²", minArea: 150, maxArea: 250 },
        { label: "250-450 м²", minArea: 250, maxArea: 450 },
        { label: "Более 450 м²", minArea: 450, maxArea: 700 },
      ],
    },
    {
      id: "scope",
      title: "Какой этап нужен?",
      goal: "От этапа зависит глубина чертежей и сроки.",
      options: [
        {
          label: "Эскизный проект",
          note: "Концепция, планы, фасады и образ здания",
          pricePerMeter: 900,
          timeline: "3-6 недель",
        },
        {
          label: "Рабочая документация",
          note: "Комплект чертежей для строителей",
          pricePerMeter: 1500,
          timeline: "6-12 недель",
        },
        {
          label: "Архитектура под ключ",
          note: "От концепции до рабочей базы",
          pricePerMeter: 2200,
          timeline: "10-18 недель",
        },
        {
          label: "Архитектурная консультация",
          note: "Разбор идеи, участка или планировки",
          fixedPrice: 18000,
          timeline: "1-3 дня",
        },
      ],
    },
    {
      id: "style",
      title: "Какой образ ближе?",
      goal: "Поможет быстрее собрать фасады и материалы.",
      options: [
        {
          label: "Современная архитектура",
          note: "Лаконичные объемы, стекло, камень",
          visual: "modern",
        },
        {
          label: "Неоклассика",
          note: "Симметрия, мягкая статусность",
          visual: "classic",
        },
        {
          label: "Шале / натуральные материалы",
          note: "Дерево, камень, теплый силуэт",
          visual: "eco",
        },
        {
          label: "Нужна авторская концепция",
          note: "Пока нет четкого направления",
          visual: "consult",
        },
      ],
    },
    commonStartQuestion,
  ],
  visualization: [
    {
      id: "object",
      title: "Что нужно визуализировать?",
      goal: "Подберем подходящий пайплайн и состав ракурсов.",
      options: [
        {
          label: "Интерьер",
          note: "Квартира, дом, офис или коммерческая зона",
        },
        { label: "Коттедж / фасады", note: "Экстерьер и посадка на участок" },
        { label: "ЖК / девелопмент", note: "Маркетинговые ракурсы для продаж" },
        { label: "360-тур", note: "Интерактивная прогулка по объекту" },
      ],
    },
    {
      id: "area",
      title: "Сколько ракурсов или зон нужно?",
      goal: "Так расчет будет ближе к реальному объему.",
      options: [
        { label: "1-2 ракурса", minArea: 1, maxArea: 2 },
        { label: "3-5 ракурсов", minArea: 3, maxArea: 5 },
        { label: "6-10 ракурсов", minArea: 6, maxArea: 10 },
        { label: "Большая серия", minArea: 10, maxArea: 18 },
      ],
    },
    {
      id: "scope",
      title: "Что уже есть на старте?",
      goal: "От исходников зависит скорость и стоимость.",
      options: [
        {
          label: "Есть чертежи и ТЗ",
          note: "Можно быстро запускать модель",
          fixedPrice: 35000,
          timeline: "5-7 дней",
        },
        {
          label: "Есть 3D-модель",
          note: "Нужны материалы, свет и финализация",
          fixedPrice: 25000,
          timeline: "3-5 дней",
        },
        {
          label: "Есть только идея",
          note: "Поможем собрать ТЗ и образ",
          fixedPrice: 65000,
          timeline: "7-14 дней",
        },
        {
          label: "Нужны материалы для продаж",
          note: "Ракурсы, кадрирование и подача",
          fixedPrice: 90000,
          timeline: "10-18 дней",
        },
      ],
    },
    {
      id: "style",
      title: "Какой уровень подачи нужен?",
      goal: "Это влияет на детализацию сцены и постобработку.",
      options: [
        {
          label: "Фотореализм",
          note: "Максимально натуральный свет и материалы",
          visual: "modern",
        },
        {
          label: "Премиальная презентация",
          note: "Кадры для сайта, буклета или рекламы",
          visual: "classic",
        },
        {
          label: "Быстрое согласование",
          note: "Понятная подача без лишнего декора",
          visual: "consult",
        },
        {
          label: "Вечерний сценарий",
          note: "Подсветка, атмосфера, окружение",
          visual: "loft",
        },
      ],
    },
    commonStartQuestion,
  ],
  landscape: [
    {
      id: "object",
      title: "Какой участок проектируем?",
      goal: "Поможет учесть сценарии отдыха и инженерные ограничения.",
      options: [
        { label: "Участок при коттедже", note: "Сад как продолжение дома" },
        { label: "Дачный участок", note: "Практичный отдых и сезонность" },
        {
          label: "КП / общественная территория",
          note: "Маршруты, въездные группы, благоустройство",
        },
        {
          label: "Только отдельная зона",
          note: "Патио, въезд, терраса или зона отдыха",
        },
      ],
    },
    {
      id: "area",
      title: "Площадь участка",
      goal: "Нужна для первичного расчета генплана и визуализации.",
      options: [
        { label: "До 6 соток", minArea: 400, maxArea: 600 },
        { label: "6-12 соток", minArea: 600, maxArea: 1200 },
        { label: "12-25 соток", minArea: 1200, maxArea: 2500 },
        { label: "Более 25 соток", minArea: 2500, maxArea: 5000 },
      ],
    },
    {
      id: "scope",
      title: "Что важно включить?",
      goal: "Соберем состав проекта без лишних этапов.",
      options: [
        {
          label: "Генплан и зонирование",
          note: "Маршруты, площадки, логика участка",
          pricePerMeter: 120,
          timeline: "3-5 недель",
        },
        {
          label: "Озеленение и дендроплан",
          note: "Растения, сезонность, уход",
          pricePerMeter: 90,
          timeline: "2-4 недели",
        },
        {
          label: "Инженерия участка",
          note: "Дренаж, автополив, свет",
          pricePerMeter: 140,
          timeline: "3-6 недель",
        },
        {
          label: "Ландшафт под ключ",
          note: "Генплан, растения, инженерия и сопровождение",
          pricePerMeter: 240,
          timeline: "6-10 недель",
        },
      ],
    },
    {
      id: "style",
      title: "Какой характер сада ближе?",
      goal: "Так проще выбрать растения, материалы и ритм.",
      options: [
        {
          label: "Современный минимализм",
          note: "Четкая геометрия и спокойные посадки",
          visual: "modern",
        },
        {
          label: "Английский сад",
          note: "Живописность, мягкие линии, цветение",
          visual: "classic",
        },
        {
          label: "Скандинавский / эко",
          note: "Натуральность и простой уход",
          visual: "eco",
        },
        {
          label: "Пока не понимаю",
          note: "Нужна консультация и подбор образа",
          visual: "consult",
        },
      ],
    },
    commonStartQuestion,
  ],
};

const inputCls =
  "w-full rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-[#F5F2EC] outline-none transition placeholder:text-white/25 focus:border-[#D69A66]/60 focus:bg-white/[0.07]";

const formatRub = (value: number) =>
  new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    maximumFractionDigits: 0,
  }).format(value);

function StylePreview({ type }: { type?: QuizOption["visual"] }) {
  if (!type) return null;

  const previewClass = {
    modern: "from-[#F5F2EC] via-[#8A9186] to-[#15130f]",
    classic: "from-[#E8DDCE] via-[#D69A66] to-[#5E5142]",
    loft: "from-[#2D2A25] via-[#8F6A4B] to-[#111111]",
    eco: "from-[#F5F2EC] via-[#75816D] to-[#2E3A2D]",
    consult: "from-[#D69A66] via-[#F5F2EC] to-[#171511]",
  }[type];

  return (
    <span
      className={`relative h-10 w-14 shrink-0 overflow-hidden rounded-2xl bg-gradient-to-br ${previewClass}`}
    >
      <span className="absolute left-2 top-2 h-2 w-8 rounded-full bg-black/20" />
      <span className="absolute bottom-2 right-2 h-4 w-4 rounded-full border border-white/50" />
    </span>
  );
}

function buildMockEstimate(answers: Answers, defaultTimeline: string) {
  const area = answers.area;
  const scope = answers.scope;
  const minArea = area?.minArea ?? 50;
  const maxArea = area?.maxArea ?? 100;
  const pricePerMeter = scope?.pricePerMeter ?? 1200;
  const min = scope?.fixedPrice ?? Math.round(minArea * pricePerMeter);
  const max = scope?.fixedPrice
    ? scope.fixedPrice * 2
    : Math.round(maxArea * pricePerMeter);

  return {
    price: `${formatRub(min)} - ${formatRub(max)}`,
    timeline: scope?.timeline ?? defaultTimeline,
  };
}

export default function ProjectQuiz({
  kind = "interior",
  serviceTitle,
}: {
  kind?: ServiceQuizKind;
  serviceTitle?: string;
}) {
  const { messengerLinks } = useCms();
  const text = useCmsText();
  const questions = useMemo(
    () =>
      quizQuestionSets[kind].map((question) => {
        const baseKey = `quiz.${kind}.${question.id}`;

        return {
          ...question,
          title: text(`${baseKey}.title`, question.title),
          goal: text(`${baseKey}.goal`, question.goal),
          options: question.options.map((option, index) => {
            const optionKey = `${baseKey}.option${index + 1}`;

            return {
              ...option,
              label: text(`${optionKey}.label`, option.label),
              note: option.note
                ? text(`${optionKey}.note`, option.note)
                : undefined,
              timeline: option.timeline
                ? text(`${optionKey}.timeline`, option.timeline)
                : undefined,
            };
          }),
        };
      }),
    [kind, text],
  );
  const [step, setStep] = useState(0);
  const [answers, setAnswers] = useState<Answers>({});
  const [name, setName] = useState("");
  const [contact, setContact] = useState("");
  const [agreed, setAgreed] = useState(true);
  const [submittedTo, setSubmittedTo] = useState<string | null>(null);
  const [showContactError, setShowContactError] = useState(false);

  const currentQuestion = questions[step];
  const isContactStep = step >= questions.length;
  const estimate = useMemo(
    () => buildMockEstimate(answers, text("quiz.defaultTimeline", "3-12 недель")),
    [answers, text],
  );
  const progress = Math.round(
    (Math.min(step, questions.length) / questions.length) * 100,
  );

  const selectOption = (option: QuizOption) => {
    setAnswers((current) => ({ ...current, [currentQuestion.id]: option }));
    setStep((current) => Math.min(current + 1, questions.length));
  };

  useEffect(() => {
    setStep(0);
    setAnswers({});
    setSubmittedTo(null);
    setShowContactError(false);
  }, [kind, serviceTitle]);

  const submitMockLead = async (channel: "MAX" | "Telegram") => {
    if (!contact.trim() || !agreed) {
      setShowContactError(true);
      return;
    }

    const payload = {
      source: "project-cost-quiz",
      channel,
      service: serviceTitle ?? kind,
      name: name.trim(),
      contact: contact.trim(),
      answers: Object.fromEntries(
        Object.entries(answers).map(([key, value]) => [key, value.label]),
      ),
      estimate,
      createdAt: new Date().toISOString(),
    };

    await submitLead({
      source: "project-cost-quiz",
      channel,
      service: serviceTitle ?? kind,
      name: name.trim(),
      contact: contact.trim(),
      payload,
    });

    localStorage.setItem("projectQuizLead", JSON.stringify(payload));
    setSubmittedTo(channel);
    setShowContactError(false);
    window.open(
      channel === "Telegram" ? messengerLinks.telegram : messengerLinks.max,
      "_blank",
      "noopener,noreferrer",
    );
  };

  return (
    <section
      id="project-quiz"
      className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16"
    >
      <div className="mx-auto max-w-7xl">
        <SectionLabel>{text("quiz.sectionLabel", "Project quiz")}</SectionLabel>

        <div className="mb-12 grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
          <div>
            <h2 className="max-w-5xl text-5xl font-light tracking-[-0.055em] md:text-7xl">
              {text("quiz.titlePrefix", "Рассчитайте стоимость и сроки")}
              {serviceTitle ? `: ${serviceTitle}` : text("quiz.titleDefaultProject", " вашего проекта")}{" "}
              {text("quiz.titleSuffix", "за 1 минуту")}
            </h2>
            <p className="mt-6 max-w-3xl text-lg leading-relaxed text-[#D6D1CA]">
              {text(
                "quiz.intro",
                "Ответьте на 5 вопросов, и мы подготовим персональное предложение. Бонусом отправим PDF-чек-лист «Подготовка к ремонту: с чего начать».",
              )}
            </p>
          </div>

          <GlassPanel className="rounded-[2rem] p-6">
            <p className="text-xs uppercase tracking-[0.28em] text-[#D69A66]">
              {text("quiz.estimateLabel", "Предварительный ориентир")}
            </p>
            <div className="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
              <div>
                <span className="block text-sm text-white/35">{text("quiz.priceLabel", "Стоимость")}</span>
                <strong className="mt-1 block text-2xl font-light tracking-[-0.04em] text-[#F5F2EC]">
                  {estimate.price}
                </strong>
              </div>
              <div>
                <span className="block text-sm text-white/35">{text("quiz.timelineLabel", "Сроки")}</span>
                <strong className="mt-1 block text-2xl font-light tracking-[-0.04em] text-[#F5F2EC]">
                  {estimate.timeline}
                </strong>
              </div>
            </div>
          </GlassPanel>
        </div>

        <GlassPanel className="overflow-hidden rounded-[2rem]">
          <div className="grid min-h-[620px] lg:grid-cols-[0.42fr_0.58fr]">
            <div className="relative flex flex-col justify-between overflow-hidden border-b border-white/10 bg-[#0c0b09]/65 p-7 md:p-9 lg:border-b-0 lg:border-r">
              <img
                src="/images/cms/quiz-interior.webp"
                alt={text("quiz.imageAlt", "Интерьер с мягким светом и натуральными материалами")}
                className="absolute inset-0 h-full w-full object-cover opacity-30"
              />
              <div className="absolute inset-0 bg-gradient-to-br from-[#0c0b09]/95 via-[#0c0b09]/76 to-[#2A3028]/58" />

              <div className="relative z-10">
                <div className="mb-6 flex items-center justify-between text-xs uppercase tracking-[0.28em] text-white/35">
                  <span>
                    {isContactStep
                      ? text("quiz.finalStep", "Финал")
                      : text("quiz.questionProgress", "Вопрос {current} из {total}")
                          .replace("{current}", String(step + 1))
                          .replace("{total}", String(questions.length))}
                  </span>
                  <span>{progress}%</span>
                </div>
                <div className="h-1 overflow-hidden rounded-full bg-white/10">
                  <div
                    className="h-full rounded-full bg-[#D69A66] transition-all duration-500"
                    style={{ width: `${progress}%` }}
                  />
                </div>

                <div className="mt-10 space-y-3">
                  {questions.map((question, index) => (
                    <button
                      key={question.id}
                      type="button"
                      onClick={() => setStep(index)}
                      className={`flex w-full items-center justify-between rounded-2xl border px-4 py-3 text-left text-sm transition ${
                        index === step
                          ? "border-[#D69A66]/55 bg-[#D69A66]/10 text-[#F5F2EC]"
                          : answers[question.id]
                            ? "border-white/10 bg-white/[0.04] text-white/60 hover:border-white/20"
                            : "border-white/8 bg-black/10 text-white/30"
                      }`}
                    >
                      <span className="pr-3">{question.title}</span>
                      <span className="text-[#D69A66]">
                        {answers[question.id] ? "✓" : `0${index + 1}`}
                      </span>
                    </button>
                  ))}
                </div>
              </div>

              <p className="relative z-10 mt-10 text-sm leading-relaxed text-[#D6D1CA]/75">
                {text(
                  "quiz.sidebarText",
                  "Ответы помогают быстро понять масштаб, сроки и формат проекта. После отправки мы свяжемся с вами в выбранном канале.",
                )}
              </p>
            </div>

            <div className="flex flex-col justify-between p-7 md:p-9">
              {!isContactStep ? (
                <>
                  <div>
                    <p className="mb-4 text-xs uppercase tracking-[0.32em] text-[#D69A66]">
                      {currentQuestion.goal}
                    </p>
                    <h3 className="max-w-3xl text-4xl font-light leading-tight tracking-[-0.045em] text-[#F5F2EC] md:text-5xl">
                      {currentQuestion.title}
                    </h3>
                  </div>

                  <div className="mt-10 grid gap-3">
                    {currentQuestion.options.map((option) => {
                      const selected =
                        answers[currentQuestion.id]?.label === option.label;

                      return (
                        <button
                          key={option.label}
                          type="button"
                          onClick={() => selectOption(option)}
                          className={`group flex min-h-20 items-center gap-4 rounded-2xl border p-4 text-left transition duration-300 ${
                            selected
                              ? "border-[#D69A66]/70 bg-[#D69A66]/12"
                              : "border-white/10 bg-white/[0.025] hover:-translate-y-0.5 hover:border-[#D69A66]/45 hover:bg-white/[0.05]"
                          }`}
                        >
                          <StylePreview type={option.visual} />
                          <span className="min-w-0 flex-1">
                            <span className="block text-lg font-light leading-tight text-[#F5F2EC]">
                              {option.label}
                            </span>
                            {option.note && (
                              <span className="mt-1 block text-sm leading-relaxed text-[#D6D1CA]/65">
                                {option.note}
                              </span>
                            )}
                          </span>
                          <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/10 text-sm text-[#D69A66] transition group-hover:border-[#D69A66]/50">
                            {selected ? "✓" : "→"}
                          </span>
                        </button>
                      );
                    })}
                  </div>

                  <div className="mt-8 flex items-center justify-between gap-3">
                    <button
                      type="button"
                      onClick={() =>
                        setStep((current) => Math.max(current - 1, 0))
                      }
                      disabled={step === 0}
                      className="rounded-full border border-white/10 px-5 py-3 text-xs uppercase tracking-[0.22em] text-white/55 transition hover:border-white/25 hover:text-white disabled:pointer-events-none disabled:opacity-35"
                    >
                      {text("quiz.backButton", "Назад")}
                    </button>
                    <button
                      type="button"
                      onClick={() => setStep(questions.length)}
                      disabled={Object.keys(answers).length < questions.length}
                      className="rounded-full border border-[#D69A66]/40 px-5 py-3 text-xs uppercase tracking-[0.22em] text-[#D69A66] transition hover:bg-[#D69A66] hover:text-[#050505] disabled:pointer-events-none disabled:opacity-35"
                    >
                      {text("quiz.toEstimateButton", "К расчету")}
                    </button>
                  </div>
                </>
              ) : (
                <div className="flex min-h-full flex-col justify-between">
                  <div>
                    <p className="mb-4 text-xs uppercase tracking-[0.32em] text-[#D69A66]">
                      {text("quiz.finalEyebrow", "Спасибо! Мы уже начали расчет вашего проекта.")}
                    </p>
                    <h3 className="max-w-3xl text-4xl font-light leading-tight tracking-[-0.045em] text-[#F5F2EC] md:text-5xl">
                      {text("quiz.finalTitle", "Укажите, куда прислать расчет и ваш бонус")}
                    </h3>
                    <p className="mt-5 max-w-2xl text-base leading-relaxed text-[#D6D1CA]">
                      {text(
                        "quiz.finalText",
                        "Оставьте контакт, а мы подготовим персональное предложение и PDF-чек-лист по старту ремонта.",
                      )}
                    </p>
                  </div>

                  <div className="mt-10 grid gap-3">
                    <input
                      className={inputCls}
                      placeholder={text("quiz.namePlaceholder", "Ваше имя")}
                      value={name}
                      onChange={(event) => setName(event.target.value)}
                    />
                    <input
                      className={inputCls}
                      placeholder={text("quiz.contactPlaceholder", "Телефон, e-mail или @username")}
                      value={contact}
                      onChange={(event) => {
                        setContact(event.target.value);
                        setShowContactError(false);
                      }}
                    />
                    {showContactError && (
                      <p className="text-sm text-[#D69A66]">
                        {text(
                          "quiz.contactError",
                          "Добавьте контакт и подтвердите согласие, чтобы мы знали, куда отправить расчет.",
                        )}
                      </p>
                    )}

                    <div className="mt-3 grid gap-3 sm:grid-cols-2">
                      <button
                        type="button"
                        onClick={() => submitMockLead("MAX")}
                        className="rounded-2xl border border-[#D69A66]/45 bg-[#D69A66] px-5 py-4 text-sm font-medium uppercase tracking-[0.16em] text-[#050505] transition hover:bg-[#F5F2EC]"
                      >
                        {text("quiz.maxButton", "Получить расчет в MAX")}
                      </button>
                      <button
                        type="button"
                        onClick={() => submitMockLead("Telegram")}
                        className="rounded-2xl border border-[#D69A66]/45 bg-white/[0.04] px-5 py-4 text-sm font-medium uppercase tracking-[0.16em] text-[#D69A66] transition hover:bg-[#D69A66] hover:text-[#050505]"
                      >
                        {text("quiz.telegramButton", "Получить расчет в Telegram")}
                      </button>
                    </div>

                    <label className="mt-3 flex cursor-pointer items-start gap-3">
                      <input
                        type="checkbox"
                        checked={agreed}
                        onChange={(event) => setAgreed(event.target.checked)}
                        className="mt-1 accent-[#D69A66]"
                      />
                      <span className="text-xs leading-relaxed text-white/40">
                        {text("quiz.consentStart", "Я ознакомился(-ась) с")}{" "}
                        <Link
                          href="/user/agreement"
                          target="_blank"
                          className="text-[#D69A66]/70 underline underline-offset-2"
                        >
                          {text("quiz.agreement", "пользовательским соглашением")}
                        </Link>{" "}
                        {text("quiz.and", "и")}{" "}
                        <Link
                          href="/politika-konfidencialnosti"
                          target="_blank"
                          className="text-[#D69A66]/70 underline underline-offset-2"
                        >
                          {text("quiz.privacy", "политикой конфиденциальности")}
                        </Link>
                        .
                      </span>
                    </label>
                    {submittedTo && (
                      <p className="rounded-2xl border border-[#D69A66]/25 bg-[#D69A66]/10 px-4 py-3 text-sm text-[#F5F2EC]">
                        {text(
                          "quiz.submitted",
                          "Заявка сохранена. Мы свяжемся с вами в выбранном канале: {channel}.",
                        ).replace("{channel}", submittedTo)}
                      </p>
                    )}
                  </div>

                  <div className="mt-8 flex items-center justify-between gap-3">
                    <button
                      type="button"
                      onClick={() => setStep(questions.length - 1)}
                      className="rounded-full border border-white/10 px-5 py-3 text-xs uppercase tracking-[0.22em] text-white/55 transition hover:border-white/25 hover:text-white"
                    >
                      {text("quiz.backButton", "Назад")}
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        setAnswers({});
                        setName("");
                        setContact("");
                        setSubmittedTo(null);
                        setStep(0);
                      }}
                      className="rounded-full border border-white/10 px-5 py-3 text-xs uppercase tracking-[0.22em] text-white/55 transition hover:border-white/25 hover:text-white"
                    >
                      {text("quiz.restartButton", "Пройти заново")}
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        </GlassPanel>
      </div>
    </section>
  );
}
