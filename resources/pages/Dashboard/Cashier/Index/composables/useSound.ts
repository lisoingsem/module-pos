type SoundType = 'add' | 'remove' | 'success' | 'error' | 'clear' | 'checkout';

// Disable sounds for now - can be enabled when sound files are added
const soundEnabled = false;
const volume = 0.3;

export function useSound() {
    function play(type: SoundType) {
        if (!soundEnabled) return;

        try {
            const audio = new Audio(`/sounds/pos-${type}.mp3`);
            audio.volume = volume;
            audio.play().catch(() => {
                // Silently fail if sound not available
            });
        } catch {
            // Silently fail
        }
    }

    return {
        play,
    };
}

