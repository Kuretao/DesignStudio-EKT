"use client";

import Link from "next/link";
import { useCms, useCmsText } from "@/src/cms";
import { GlassPanel } from "@/src/ui";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import SectionLabel from "@/src/components/common/SectionLabel";
import { imageFrames, projectImageAt } from "@/src/utils/images";

export default function AboutPageFull() {
  const { projects } = useCms();
  const text = useCmsText();

  const stats = [
    { value: text("aboutFull.stats.1.value", "10 лет"), label: text("aboutFull.stats.1.label", "проектируем интерьеры, архитектуру и визуализации") },
    { value: text("aboutFull.stats.2.value", "25+"), label: text("aboutFull.stats.2.label", "специалистов подключаются под масштаб задачи") },
    { value: text("aboutFull.stats.3.value", "90%"), label: text("aboutFull.stats.3.label", "клиентов возвращаются с новыми проектами") },
    { value: text("aboutFull.stats.4.value", "1500+"), label: text("aboutFull.stats.4.label", "задач в год: от рендера до полного сопровождения") },
  ];

  const directions = [
    text("aboutFull.directions.1", "Дизайн интерьера"),
    text("aboutFull.directions.2", "Коммерческие пространства"),
    text("aboutFull.directions.3", "Архитектурное проектирование"),
    text("aboutFull.directions.4", "Архитектурная 3D-визуализация"),
    text("aboutFull.directions.5", "Ландшафтный дизайн"),
    text("aboutFull.directions.6", "Комплектация объекта"),
  ];

  const principles = [
    {
      title: text("aboutFull.principles.item.1.title", "Сначала сценарий"),
      text: text("aboutFull.principles.item.1.text", "Мы начинаем не с красивой картинки, а с того, как человек будет жить, работать, принимать гостей и двигаться по пространству."),
    },
    {
      title: text("aboutFull.principles.item.2.title", "Красота держится на чертежах"),
      text: text("aboutFull.principles.item.2.text", "Визуальная идея сразу проверяется планировкой, материалами, светом, узлами, бюджетом и возможностью реализации."),
    },
    {
      title: text("aboutFull.principles.item.3.title", "Проект не бросается после рендера"),
      text: text("aboutFull.principles.item.3.text", "Помогаем с комплектацией, заменами, подрядчиками и авторским сопровождением, чтобы результат не распался на стройке."),
    },
    {
      title: text("aboutFull.principles.item.4.title", "Удаленно тоже точно"),
      text: text("aboutFull.principles.item.4.text", "Работаем с клиентами из разных городов через видео-брифы, 3D-презентации, облачные доски и понятные пакеты документации."),
    },
  ];

  const teamBullets = [
    text("aboutFull.team.bullet.1", "единая коммуникация"),
    text("aboutFull.team.bullet.2", "понятные этапы"),
    text("aboutFull.team.bullet.3", "фиксированный состав работ"),
    text("aboutFull.team.bullet.4", "реалистичная визуальная подача"),
  ];

  const workflow = [
    ["01", text("aboutFull.process.step.1.title", "Бриф"), text("aboutFull.process.step.1.text", "Фиксируем задачу, стиль, бюджет, сроки, состав помещений и ограничения объекта.")],
    ["02", text("aboutFull.process.step.2.title", "Концепция"), text("aboutFull.process.step.2.text", "Собираем планировку, референсы, материалы, свет и первый визуальный язык проекта.")],
    ["03", text("aboutFull.process.step.3.title", "3D и согласование"), text("aboutFull.process.step.3.text", "Показываем будущий результат до закупок и ремонта, спокойно проходим итерации правок.")],
    ["04", text("aboutFull.process.step.4.title", "Документация"), text("aboutFull.process.step.4.text", "Готовим чертежи, ведомости, спецификации и техническую базу для подрядчиков.")],
    ["05", text("aboutFull.process.step.5.title", "Комплектация"), text("aboutFull.process.step.5.text", "Подбираем материалы, мебель, свет, аналоги и поставщиков под согласованный бюджет.")],
    ["06", text("aboutFull.process.step.6.title", "Сопровождение"), text("aboutFull.process.step.6.text", "Отвечаем на вопросы реализации и помогаем сохранить авторскую идею в реальном объекте.")],
  ];

  return (
    <div className="page-in">
      <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
        <HeroBackdropSlider
          slides={[
            { image: projectImageAt(projects, 0), alt: "3D Smart Design Studio" },
            { image: projectImageAt(projects, 1), alt: "Интерьерный проект студии" },
            { image: projectImageAt(projects, 4), alt: "Архитектурная визуализация студии" },
          ]}
        />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.97)_0%,rgba(5,5,5,.72)_48%,rgba(5,5,5,.2)_100%)]" />
        <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.36)_32%,transparent_76%)]" />

        <div className="relative z-10 mx-auto grid min-h-[calc(100vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[0.94fr_1.06fr] lg:items-end">
          <div className="pb-8">
            <SectionLabel>{text("aboutFull.hero.label", "О студии")}</SectionLabel>
            <h1 className="mt-5 max-w-5xl text-[clamp(2.8rem,5vw,5.2rem)] font-light leading-[0.94] tracking-normal md:tracking-[-0.035em]">
              {text("aboutFull.hero.title", "Дизайн с умом")}
            </h1>
            <p className="mt-8 max-w-3xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
              {text(
                "aboutFull.hero.text",
                "3D Smart Design Studio соединяет эстетику, инженерную логику и понятный процесс. Заказчик видит не просто красивую картинку, а уверенный маршрут от идеи до реализации.",
              )}
            </p>
            <div className="mt-10 flex flex-wrap gap-3">
              <Link href="/kontakty" className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]">
                {text("aboutFull.hero.cta1", "Обсудить проект")}
              </Link>
              <Link href="/portfolio" className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white">
                {text("aboutFull.hero.cta2", "Смотреть работы")}
              </Link>
            </div>
          </div>

          <div className="mb-8 grid gap-4">
            <div className="grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 sm:grid-cols-3">
              {stats.slice(0, 3).map((stat) => (
                <GlassPanel key={stat.value} className="p-5">
                  <strong className="block text-3xl font-light tracking-[-0.04em] text-[#D69A66]">{stat.value}</strong>
                  <span className="mt-3 block text-xs uppercase leading-relaxed tracking-[0.16em] text-[#D6D1CA]">{stat.label}</span>
                </GlassPanel>
              ))}
            </div>

            <GlassPanel className="overflow-hidden rounded-[2rem] p-0">
              <div className="grid md:grid-cols-[0.95fr_1.05fr]">
                <div className="relative min-h-72 overflow-hidden">
                  <CinematicImage frames={[projectImageAt(projects, 4), projectImageAt(projects, 5), projectImageAt(projects, 0)]} alt="Архитектурная визуализация студии" fill hint="system" />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/84 via-transparent to-transparent" />
                  <p className="absolute bottom-5 left-5 right-5 text-2xl font-light tracking-[-0.04em]">{text("aboutFull.hero.imageLabel", "Премиальная подача начинается с точной системы")}</p>
                </div>
                <div className="p-6 md:p-7">
                  <p className="text-xs uppercase tracking-[0.35em] text-[#D69A66]">{text("aboutFull.hero.approachLabel", "Что внутри подхода")}</p>
                  <div className="mt-6 grid gap-3">
                    {directions.map((direction) => (
                      <div key={direction} className="flex items-center justify-between border-b border-white/10 pb-3">
                        <span className="text-sm text-[#F5F2EC]">{direction}</span>
                        <span className="text-[#D69A66]">→</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </GlassPanel>
          </div>
        </div>
      </section>

      <section className="border-t border-white/10 px-5 py-20 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.24)] md:grid-cols-4">
          {stats.map((stat) => (
            <GlassPanel key={stat.value} className="p-7">
              <strong className="block text-4xl font-light tracking-[-0.05em] text-[#D69A66] md:text-5xl">{stat.value}</strong>
              <p className="mt-5 text-sm leading-relaxed text-[#D6D1CA]">{stat.label}</p>
            </GlassPanel>
          ))}
        </div>
      </section>

      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr]">
          <div>
            <SectionLabel>{text("aboutFull.principles.label", "Принципы")}</SectionLabel>
            <h2 className="text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-tight tracking-normal md:tracking-[-0.035em]">{text("aboutFull.principles.title", "Не декорируем хаос. Собираем систему.")}</h2>
            <p className="mt-6 text-lg leading-relaxed text-[#D6D1CA]">
              {text(
                "aboutFull.principles.text",
                "Хороший проект ощущается легко, потому что за ним стоит точная работа: планировочная логика, материалы, свет, документация, бюджет и коммуникация.",
              )}
            </p>
          </div>
          <div className="grid gap-4 md:grid-cols-2">
            {principles.map((item, index) => (
              <GlassPanel key={item.title} className="rounded-[1.5rem] p-6 transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/50">
                <span className="text-sm text-[#D69A66]">0{index + 1}</span>
                <h3 className="mt-8 text-2xl font-light tracking-[-0.035em]">{item.title}</h3>
                <p className="mt-4 text-sm leading-relaxed text-[#D6D1CA]">{item.text}</p>
              </GlassPanel>
            ))}
          </div>
        </div>
      </section>

      <section className="overflow-hidden border-y border-white/10 px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
          <div className="grid gap-5 md:grid-cols-2">
            <div className="group relative min-h-[540px] overflow-hidden rounded-[2rem] border border-white/10">
              <CinematicImage frames={[projectImageAt(projects, 1), projectImageAt(projects, 0), projectImageAt(projects, 3)]} alt="Интерьерный проект" fill hint="motion" />
              <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/85 via-transparent to-transparent" />
              <p className="absolute bottom-6 left-6 right-6 text-2xl font-light">{text("aboutFull.work.imageLabel1", "Интерьер как рабочий сценарий жизни")}</p>
            </div>
            <div className="grid gap-5">
              <div className="group relative min-h-[260px] overflow-hidden rounded-[2rem] border border-white/10">
                <CinematicImage frames={[projectImageAt(projects, 5), projectImageAt(projects, 4), projectImageAt(projects, 1)]} alt="Архитектурный проект" fill hint="motion" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/80 via-transparent to-transparent" />
                <p className="absolute bottom-5 left-5 right-5 text-xl font-light">{text("aboutFull.work.imageLabel2", "Архитектура и визуализация")}</p>
              </div>
              <div className="group relative min-h-[260px] overflow-hidden rounded-[2rem] border border-white/10">
                <CinematicImage frames={[projectImageAt(projects, 2), projectImageAt(projects, 3), projectImageAt(projects, 0)]} alt="Ландшафтный дизайн" fill hint="motion" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/80 via-transparent to-transparent" />
                <p className="absolute bottom-5 left-5 right-5 text-xl font-light">{text("aboutFull.work.imageLabel3", "Ландшафт как продолжение дома")}</p>
              </div>
            </div>
          </div>
          <div>
            <SectionLabel>{text("aboutFull.team.label", "Команда")}</SectionLabel>
            <h2 className="text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-tight tracking-normal md:tracking-[-0.035em]">{text("aboutFull.team.title", "Под задачу собирается нужный состав")}</h2>
            <p className="mt-6 text-lg leading-relaxed text-[#D6D1CA]">
              {text(
                "aboutFull.team.text",
                "В проект могут входить дизайнеры, архитекторы, 3D-визуализаторы, комплектаторы и специалисты по рабочей документации. Клиенту не нужно координировать всех отдельно: мы держим проектную логику внутри студии.",
              )}
            </p>
            <div className="mt-8 grid gap-3">
              {teamBullets.map((item) => (
                <div key={item} className="rounded-2xl border border-white/10 bg-white/[0.03] px-5 py-4 text-[#D6D1CA]">
                  {item}
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <div className="mb-12 grid gap-8 md:grid-cols-[0.8fr_1.2fr] md:items-end">
            <div>
              <SectionLabel>{text("aboutFull.process.label", "Процесс")}</SectionLabel>
              <h2 className="text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-tight tracking-normal md:tracking-[-0.035em]">{text("aboutFull.process.title", "Как мы работаем")}</h2>
            </div>
            <p className="text-lg leading-relaxed text-[#D6D1CA]">
              {text(
                "aboutFull.process.text",
                "Каждый этап дает понятный результат: от первого разговора до комплекта файлов, с которым можно принимать решения и двигаться дальше.",
              )}
            </p>
          </div>
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {workflow.map(([number, title, workflowText]) => (
              <div key={number} className="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.025] p-7 transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/45">
                <span className="text-sm text-[#D69A66]">{number}</span>
                <h3 className="mt-10 text-2xl font-light">{title}</h3>
                <p className="mt-4 text-sm leading-relaxed text-[#D6D1CA]">{workflowText}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 pb-28 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl overflow-hidden rounded-[2.5rem] border border-white/10 bg-[#D69A66] p-8 text-[#050505] md:p-12 lg:p-16">
          <div className="grid gap-8 md:grid-cols-[1fr_auto] md:items-end">
            <div>
              <p className="mb-4 text-xs uppercase tracking-[0.35em] opacity-60">{text("aboutFull.cta.eyebrow", "Начнем с разговора")}</p>
              <h2 className="max-w-3xl text-4xl font-light tracking-[-0.045em] md:text-6xl">
                {text("aboutFull.cta.title", "Расскажите о проекте, а мы предложим маршрут работы.")}
              </h2>
            </div>
            <Link href="/kontakty" className="inline-flex h-14 items-center justify-center rounded-full bg-[#050505] px-7 text-xs uppercase tracking-[0.24em] text-[#F5F2EC] transition hover:bg-[#1A1A1A]">
              {text("aboutFull.cta.button", "Связаться")}
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
