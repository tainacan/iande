import { DateTime, Interval } from 'luxon'

const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday',
'friday', 'saturday']

function isValidInterval (interval) {
    return interval && interval.from && interval.to && interval.from < interval.to
}

function toArray (value) {
    if (!value) {
        return []
    } else if (Array.isArray(value)) {
        return value
    } else {
        return Object.values(value)
    }
}

function normalizeDate (date) {
    if (date instanceof DateTime) {
        return date
    } else if (date instanceof Date) {
        return DateTime.fromJSDate(date)
    } else {
        return DateTime.fromISO(date)
    }
}

function normalizeTime (time) {
    if (time instanceof DateTime) {
        return time
    } else if (time instanceof Date) {
        return DateTime.fromJSDate(time)
    } else {
        return DateTime.fromFormat(time, 'HH:mm')
    }
}

export function getWorkingHours (exhibition, date) {
    const dt = normalizeDate(date)
    const dateString = dt.toISODate()
    for (const exception of toArray(exhibition.exception)) {
        if (dateString >= exception.date_from && (!exception.date_to || dateString <= exception.date_to)) {
            const intervals = toArray(exception.exceptions) || []
            return intervals.filter(isValidInterval)
        }
    }
    const intervals = toArray(exhibition[weekDays[dt.weekday]]) || []
    return intervals.filter(isValidInterval)
}

export function getSlots (exhibition, date) {
    const delta = { minutes: Number(exhibition.duration) }
    const intervals = getWorkingHours(exhibition, date)
    return intervals.flatMap(interval => {
        const intervalStart = normalizeTime(interval.from)
        const intervalEnd = normalizeTime(interval.to)
        return Interval.fromDateTimes(intervalStart, intervalEnd)
            .splitBy({ minutes: Number(exhibition.grid) })
            .map(subinterval => subinterval.set({ end: subinterval.start.plus(delta) }))
            .filter(subinterval => subinterval.end < intervalEnd)
    })
}
