"use client";

import { FormEvent, useEffect, useState } from "react";
import styled from "styled-components";

const ACCESS_KEY = "ekt-maintenance-access";
const ACCESS_PIN = "1208";

export default function MaintenanceGate({
  children,
  enabled = true,
}: {
  children: React.ReactNode;
  enabled?: boolean;
}) {
  const [isReady, setIsReady] = useState(false);
  const [isUnlocked, setIsUnlocked] = useState(false);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [pin, setPin] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    setIsUnlocked(window.localStorage.getItem(ACCESS_KEY) === ACCESS_PIN);
    setIsReady(true);
  }, []);

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (pin.trim() !== ACCESS_PIN) {
      setError("Неверный PIN");
      return;
    }

    window.localStorage.setItem(ACCESS_KEY, ACCESS_PIN);
    setIsUnlocked(true);
    setIsDialogOpen(false);
    setPin("");
    setError("");
  }

  if (!enabled || (isReady && isUnlocked)) {
    return <>{children}</>;
  }

  return (
    <MaintenanceScreen>
      <MaintenanceContent>
        <Eyebrow>3D Smart Design Studio</Eyebrow>
        <Title>Ведутся технические работы</Title>
        <Text>
          Мы обновляем сайт и скоро вернемся. Для связи со студией используйте
          привычные контакты.
        </Text>
      </MaintenanceContent>

      <AdminButton type="button" onClick={() => setIsDialogOpen(true)}>
        админ
      </AdminButton>

      {isDialogOpen ? (
        <DialogOverlay onMouseDown={() => setIsDialogOpen(false)}>
          <Dialog onMouseDown={(event) => event.stopPropagation()}>
            <DialogTitle>Вход на сайт</DialogTitle>
            <form onSubmit={handleSubmit}>
              <PinInput
                autoFocus
                inputMode="numeric"
                maxLength={8}
                placeholder="PIN"
                value={pin}
                onChange={(event) => {
                  setPin(event.target.value);
                  setError("");
                }}
              />
              {error ? <ErrorText>{error}</ErrorText> : null}
              <DialogActions>
                <GhostButton type="button" onClick={() => setIsDialogOpen(false)}>
                  Отмена
                </GhostButton>
                <SubmitButton type="submit">Войти</SubmitButton>
              </DialogActions>
            </form>
          </Dialog>
        </DialogOverlay>
      ) : null}
    </MaintenanceScreen>
  );
}

const MaintenanceScreen = styled.main`
  position: fixed;
  inset: 0;
  z-index: 2147483647;
  display: grid;
  place-items: center;
  min-height: 100vh;
  padding: 24px;
  overflow: hidden;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.035), transparent 34%),
    #0b0a08;
  color: #f4efe8;
`;

const MaintenanceContent = styled.section`
  width: min(760px, 100%);
  text-align: center;
`;

const Eyebrow = styled.p`
  margin: 0 0 22px;
  color: #d9a064;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.34em;
  text-transform: uppercase;
`;

const Title = styled.h1`
  margin: 0;
  font-size: clamp(44px, 8vw, 104px);
  font-weight: 300;
  line-height: 0.96;
`;

const Text = styled.p`
  max-width: 560px;
  margin: 24px auto 0;
  color: rgba(244, 239, 232, 0.74);
  font-size: clamp(16px, 2vw, 20px);
  line-height: 1.65;
`;

const AdminButton = styled.button`
  position: fixed;
  right: 18px;
  bottom: 16px;
  z-index: 2;
  border: 1px solid rgba(244, 239, 232, 0.12);
  border-radius: 999px;
  padding: 8px 12px;
  background: rgba(244, 239, 232, 0.035);
  color: rgba(244, 239, 232, 0.32);
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: lowercase;
  cursor: pointer;
  transition:
    color 160ms ease,
    border-color 160ms ease,
    background 160ms ease;

  &:hover,
  &:focus-visible {
    border-color: rgba(217, 160, 100, 0.56);
    background: rgba(217, 160, 100, 0.1);
    color: #f4efe8;
    outline: none;
  }
`;

const DialogOverlay = styled.div`
  position: fixed;
  inset: 0;
  z-index: 3;
  display: grid;
  place-items: center;
  padding: 20px;
  background: rgba(0, 0, 0, 0.58);
  backdrop-filter: blur(12px);
`;

const Dialog = styled.div`
  width: min(360px, 100%);
  border: 1px solid rgba(244, 239, 232, 0.16);
  border-radius: 18px;
  padding: 20px;
  background: rgba(18, 15, 12, 0.96);
  box-shadow: 0 24px 90px rgba(0, 0, 0, 0.45);
`;

const DialogTitle = styled.h2`
  margin: 0 0 14px;
  color: #fffaf3;
  font-size: 22px;
  font-weight: 500;
`;

const PinInput = styled.input`
  width: 100%;
  height: 48px;
  border: 1px solid rgba(244, 239, 232, 0.18);
  border-radius: 12px;
  padding: 0 14px;
  background: rgba(255, 255, 255, 0.04);
  color: #fffaf3;
  font-size: 18px;

  &:focus {
    border-color: rgba(217, 160, 100, 0.75);
    outline: none;
  }
`;

const ErrorText = styled.p`
  margin: 10px 0 0;
  color: #ffb4a8;
  font-size: 13px;
`;

const DialogActions = styled.div`
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 16px;
`;

const GhostButton = styled.button`
  min-height: 40px;
  border: 1px solid rgba(244, 239, 232, 0.14);
  border-radius: 999px;
  padding: 0 14px;
  background: transparent;
  color: rgba(244, 239, 232, 0.72);
  cursor: pointer;
`;

const SubmitButton = styled.button`
  min-height: 40px;
  border: 0;
  border-radius: 999px;
  padding: 0 18px;
  background: #d9a064;
  color: #120f0c;
  font-weight: 700;
  cursor: pointer;
`;
