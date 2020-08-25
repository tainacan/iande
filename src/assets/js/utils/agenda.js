import { DateTime, Interval } from 'luxon'

const duration = Number(window.IandeSettings.duration)
const schedules = window.IandeSettings.schedules
const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday',
'friday', 'saturday']

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

export function getWorkingHours (date) {
    const dt = normalizeDate(date)
    const intervals = schedules[weekDays[dt.weekday]] || []
    return intervals.filter(interval => interval && interval.from && interval.to)
}

export function getSlots (date) {
    const intervals = getWorkingHours(date)
    return intervals.flatMap(interval => {
        return Interval.fromDateTimes(
            normalizeTime(interval.from),
            normalizeTime(interval.to)
        ).splitBy({ minutes: duration })
    })
}
