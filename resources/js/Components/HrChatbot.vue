<script setup>
import { ref, nextTick, useTemplateRef } from 'vue'

const API_URL = 'http://localhost:5000/hr-chat'

const messages = ref([])
const input = ref('')
const loading = ref(false)
const scroller = useTemplateRef('scroller')

const scrollToBottom = async () => {
  await nextTick()
  scroller.value?.scrollTo({ top: scroller.value.scrollHeight, behavior: 'smooth' })
}

const send = async () => {
  const question = input.value.trim()
  if (!question || loading.value) return

  messages.value.push({ role: 'user', content: question })
  input.value = ''
  loading.value = true
  scrollToBottom()

  try {
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question }),
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const data = await res.json()
    messages.value.push({
      role: 'assistant',
      content: data.answer,
      references: data.references ?? [],
    })
  } catch (_) {
    messages.value.push({
      role: 'assistant',
      content: 'Maaf, terjadi kesalahan saat menghubungi server. Silakan coba lagi.',
      error: true,
    })
  } finally {
    loading.value = false
    scrollToBottom()
  }
}

const onEnter = (e) => {
  if (e.shiftKey) return
  e.preventDefault()
  send()
}

const bubbleClass = (msg) => {
  if (msg.error) return 'border-red-200 bg-red-50 text-red-700 rounded-bl-sm'
  if (msg.role === 'user') return 'border-blue-600 bg-blue-600 text-white rounded-br-sm'
  return 'border-slate-200 bg-white text-slate-900 rounded-bl-sm'
}
</script>

<template>
  <section
    class="flex h-dvh w-full flex-col overflow-hidden bg-white text-slate-900 sm:mx-auto sm:my-8 sm:h-[720px] sm:max-w-[480px] sm:rounded-2xl sm:border sm:border-slate-200 sm:shadow-xl sm:shadow-slate-900/10"
  >
    <header class="flex items-center gap-3 border-b border-slate-200 bg-white px-5 py-4">
      <div class="grid size-10 place-items-center rounded-full bg-blue-600 text-sm font-semibold text-white">
        HR
      </div>
      <div>
        <h2 class="text-[15px] font-semibold leading-tight">HR Assistant</h2>
        <p class="text-xs text-slate-500">CoreSpace Teknologi Indonesia</p>
      </div>
    </header>

    <div ref="scroller" class="flex flex-1 flex-col gap-3 overflow-y-auto bg-slate-50 p-5">
      <div
        v-if="!messages.length"
        class="m-auto max-w-[280px] text-center text-sm text-slate-500"
      >
        Selamat datang! Tanyakan apa saja seputar kebijakan HR perusahaan.
      </div>

      <div
        v-for="(msg, i) in messages"
        :key="i"
        class="flex"
        :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
      >
        <div
          class="max-w-[78%] rounded-2xl border px-4 py-2.5 text-sm leading-relaxed"
          :class="bubbleClass(msg)"
        >
          <p class="m-0 whitespace-pre-wrap">{{ msg.content }}</p>
          <ul
            v-if="msg.references?.length"
            class="mt-2 flex list-none flex-wrap gap-1.5 p-0"
          >
            <li
              v-for="ref in msg.references"
              :key="ref"
              class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-600"
            >
              {{ ref }}
            </li>
          </ul>
        </div>
      </div>

      <div v-if="loading" class="flex justify-start">
        <div class="rounded-2xl rounded-bl-sm border border-slate-200 bg-white px-4 py-3">
          <span class="inline-flex items-center gap-1">
            <span class="size-1.5 animate-bounce rounded-full bg-slate-400" />
            <span class="size-1.5 animate-bounce rounded-full bg-slate-400 [animation-delay:0.15s]" />
            <span class="size-1.5 animate-bounce rounded-full bg-slate-400 [animation-delay:0.3s]" />
          </span>
        </div>
      </div>
    </div>

    <form
      class="flex gap-2 border-t border-slate-200 bg-white p-3"
      @submit.prevent="send"
    >
      <textarea
        v-model="input"
        :disabled="loading"
        rows="1"
        placeholder="Tulis pertanyaan Anda..."
        class="max-h-32 flex-1 resize-none rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 disabled:opacity-60"
        @keydown.enter="onEnter"
      />
      <button
        type="submit"
        :disabled="loading || !input.trim()"
        class="rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50"
      >
        Kirim
      </button>
    </form>
  </section>
</template>
