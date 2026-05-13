interface ScoreSliderProps {
  label: string
  value: number
  onChange: (value: number) => void
  min?: number
  max?: number
  step?: number
}

export function ScoreSlider({
  label,
  value,
  onChange,
  min = 0,
  max = 10,
  step = 0.5,
}: ScoreSliderProps) {
  return (
    <div className="score-slider">
      <div className="score-slider-header">
        <span className="score-slider-label">{label}</span>
        <span className="score-slider-value">{value.toFixed(1)}</span>
      </div>
      <input
        type="range"
        min={min}
        max={max}
        step={step}
        value={value}
        onChange={(e) => onChange(parseFloat(e.target.value))}
      />
    </div>
  )
}
