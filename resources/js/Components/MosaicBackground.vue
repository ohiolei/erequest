<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const AVAILABLE_REQUESTS = [
    'Certificate',
    'Local Transcript',
    'International Transcript',
    'Proficiency In English',
    'Notification of Result',
    'Transcript Students Copy',
];

const TILE_HEIGHTS = [210, 280, 240, 320, 200, 300, 260, 340];
const TILE_TONES = [
    'from-blue-600 via-blue-500 to-sky-400',
    'from-sky-500 via-cyan-400 to-blue-400',
    'from-indigo-600 via-blue-500 to-sky-400',
    'from-blue-700 via-sky-500 to-cyan-300',
    'from-cyan-500 via-blue-400 to-indigo-400',
    'from-blue-500 via-indigo-400 to-sky-300',
    'from-sky-600 via-blue-500 to-cyan-400',
    'from-blue-800 via-blue-600 to-sky-400',
];

const mosaicDensity = ref({ columnCount: 7, tilesPerColumn: 7 });

const updateMosaicDensity = () => {
    const width = window.innerWidth;
    if (width < 640) {
        mosaicDensity.value = { columnCount: 3, tilesPerColumn: 4 };
    } else if (width < 1024) {
        mosaicDensity.value = { columnCount: 5, tilesPerColumn: 5 };
    } else {
        mosaicDensity.value = { columnCount: 7, tilesPerColumn: 7 };
    }
};

const mosaicColumns = computed(() => {
    const { columnCount, tilesPerColumn } = mosaicDensity.value;
    const columns = Array.from({ length: columnCount }, () => []);

    for (let i = 0; i < columnCount * tilesPerColumn; i++) {
        const name = AVAILABLE_REQUESTS[i % AVAILABLE_REQUESTS.length];
        columns[i % columnCount].push({
            key: `${name}-${i}`,
            name,
            height: TILE_HEIGHTS[(i + Math.floor(i / columnCount)) % TILE_HEIGHTS.length],
            tone: TILE_TONES[i % TILE_TONES.length],
        });
    }

    return columns;
});

onMounted(() => {
    updateMosaicDensity();
    window.addEventListener('resize', updateMosaicDensity, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('resize', updateMosaicDensity);
});
</script>

<template>
    <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
        <div class="mosaic absolute inset-x-0 -top-32 flex justify-center gap-2 px-1 sm:gap-3 sm:px-2">
            <div
                v-for="(column, colIndex) in mosaicColumns"
                :key="colIndex"
                class="mosaic-column flex w-[38vw] shrink-0 flex-col gap-2 sm:w-[22vw] sm:gap-3 lg:w-[13.5vw]"
                :class="colIndex % 2 === 0 ? 'mosaic-drift-a' : 'mosaic-drift-b'"
                :style="{
                    animationDelay: `${colIndex * -3}s`,
                    marginTop: `${(colIndex % 3) * 36}px`,
                }"
            >
                <div
                    v-for="tile in column"
                    :key="tile.key"
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br shadow-lg"
                    :class="tile.tone"
                    :style="{ height: `${tile.height}px` }"
                >
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.28),transparent_45%)]" />
                    <div class="absolute inset-x-4 top-5 space-y-2">
                        <div class="h-2 w-3/4 rounded-full bg-white/45" />
                        <div class="h-2 w-1/2 rounded-full bg-white/30" />
                        <div class="h-2 w-2/3 rounded-full bg-white/20" />
                    </div>
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/25 to-transparent p-4 pt-20">
                        <p class="text-[13px] font-semibold leading-snug text-white">
                            {{ tile.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute inset-0 bg-black/55" />
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/50" />
    </div>
</template>

<style scoped>
.mosaic-drift-a {
    animation: mosaic-drift 52s linear infinite;
}

.mosaic-drift-b {
    animation: mosaic-drift-reverse 60s linear infinite;
}

@keyframes mosaic-drift {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-16%);
    }
}

@keyframes mosaic-drift-reverse {
    from {
        transform: translateY(-10%);
    }
    to {
        transform: translateY(8%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .mosaic-drift-a,
    .mosaic-drift-b {
        animation: none;
    }
}
</style>
