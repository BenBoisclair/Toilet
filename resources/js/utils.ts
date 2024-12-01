import { Ref } from "vue";

export const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    };
    return date.toLocaleDateString('en-US', options);
};

export const sleep = (seconds: number) => new Promise(resolve => setTimeout(resolve, seconds * 1000));

export const debounce = (duration: number, debounceTimeoutRef: ReturnType<typeof setTimeout> | null, action: () => void) => {
    if (debounceTimeoutRef) clearTimeout(debounceTimeoutRef);
    debounceTimeoutRef = setTimeout(() => {
        action();
    }, duration);
};

const titles = [
    'Loo-cation Explorer',
    'Flush Finder Pro',
    'Toilet Treasure Map',
    'Potty Paradise Locator',
    'Where to Go? 💩',
    'The Great Loo Hunt',
    'Throne Tracker',
    'Restroom Radar',
    'Map My Throne',
    'Peeps & Seats',
];

export const rotateTitle = (currentTitleRef: Ref<string, string>, titles: string[]) => {
    let index = 0;
    setInterval(() => {
        index = (index + 1) % titles.length;
        currentTitleRef.value = titles[index];
    }, 10000);
};
