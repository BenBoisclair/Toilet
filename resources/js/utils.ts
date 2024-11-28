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
