import { DateTime, Interval } from 'luxon'

const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday',
'friday', 'saturday']

function isValidInterval (interval) {
    return interval && interval.from && interval.to
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
        if (dateString >= exception.date_from && dateString <= exception.date_to) {
            const intervals = toArray(exception.exceptions) || []
            return intervals.filter(isValidInterval)
        }
    }
    const intervals = toArray(exhibition[weekDays[dt.weekday]]) || []
    return intervals.filter(isValidInterval)
}

export function getSlots (exhibition, date) {
    const intervals = getWorkingHours(exhibition, date)
    return intervals.flatMap(interval => {
        return Interval.fromDateTimes(
            normalizeTime(interval.from),
            normalizeTime(interval.to)
        ).splitBy({ minutes: Number(exhibition.duration) })
    })
}
