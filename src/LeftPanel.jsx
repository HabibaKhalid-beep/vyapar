import { useState } from 'react'
import './LeftPanel.css'

const LeftPanel = ({ selectedTheme, setSelectedTheme, selectedColor, setSelectedColor }) => {

  const [classicOpen, setClassicOpen] = useState(true)
  const [vintageOpen, setVintageOpen] = useState(false)

  const classicThemes = [
    { id: 'tally', name: 'Tally Theme' },
    { id: 'tax1', name: 'Tax Theme 1' },
    { id: 'tax3', name: 'Tax Theme 3' },
    { id: 'divine', name: 'Double Divine' },
    { id: 'french', name: 'French Elite' },
    { id: 'LandScapeTheme1', name: 'LandScape Theme 1' },
    { id: 'LandScapeTheme2', name: 'LandScape Theme 2' },
  ]

  const vintageThemes = [
    { id: 'tax2', name: 'Tax Theme 2' },
    { id: 'tax4', name: 'Tax Theme 4' },
    { id: 'tax5', name: 'Tax Theme 5' },
    { id: 'tax6', name: 'Tax Theme 6' },
    { id: 'theme1', name: 'Theme 1' },
    { id: 'theme2', name: 'Theme 2' },
    { id: 'theme3', name: 'Theme 3' },
    { id: 'theme4', name: 'Theme 4' },
  ]

  const thermalThemes = [
    { id: 'thermal1', name: 'Thermal Theme 1' },
    { id: 'thermal2', name: 'Thermal Theme 2' },
    { id: 'thermal3', name: 'Thermal Theme 3' },
    { id: 'thermal4', name: 'Thermal Theme 4' },
    { id: 'thermal5', name: 'Thermal Theme 5' },
  ]

  const divineColors = [
    '#e63946',
    '#457b9d',
    '#e07b39',
    '#2d6a4f',
    '#6a4c93',
  ]

  const colors = [
    '#a78bfa', '#0ea5e9', '#9ca3af', '#6b7280', '#84855a', '#6366f1', '#22d3ee',
    '#16a34a', '#65a30d', '#92400e', '#7e22ce', '#be185d', '#b45309', '#d97706',
    '#f9a8d4', '#ea580c', '#dc2626', '#9333ea', '#db2777', '#f59e0b'
  ]

  const isDivine = selectedTheme === 'divine'
  const isThermal = thermalThemes.map(t => t.id).includes(selectedTheme)

  return (
    <div className="left-panel">
      <p className="panel-heading">Select Theme</p>

      <div className="category">
        <div className="category-header" onClick={() => setClassicOpen(!classicOpen)}>
          <span>Classic Themes</span>
          <span>{classicOpen ? '⌃' : '⌄'}</span>
        </div>

        {classicOpen && (
          <div className="theme-list">
            {classicThemes.map((theme) => (
              <div
                key={theme.id}
                className={`theme-item ${selectedTheme === theme.id ? 'active' : ''}`}
                onClick={() => setSelectedTheme(theme.id)}
              >
                {theme.name}
              </div>
            ))}
          </div>
        )}
      </div>

      <div className="category">
        <div className="category-header" onClick={() => setVintageOpen(!vintageOpen)}>
          <span>Vintage Themes</span>
          <span>{vintageOpen ? '⌃' : '⌄'}</span>
        </div>

        {vintageOpen && (
          <div className="theme-list">
            {vintageThemes.map((theme) => (
              <div
                key={theme.id}
                className={`theme-item ${selectedTheme === theme.id ? 'active' : ''}`}
                onClick={() => setSelectedTheme(theme.id)}
              >
                {theme.name}
              </div>
            ))}
            {thermalThemes.map((theme) => (
              <div
                key={theme.id}
                className={`theme-item ${selectedTheme === theme.id ? 'active' : ''}`}
                onClick={() => setSelectedTheme(theme.id)}
              >
                {theme.name}
              </div>
            ))}
          </div>
        )}
      </div>

      {!isThermal && (
        <div className="color-section">
          <p className="color-section-heading">Select Color</p>
          <div className="selected-color">
            <div className="color-box" style={{ backgroundColor: isDivine ? selectedColor : selectedColor }}></div>
            <span className="selected-label">Selected</span>
          </div>
          <div className={isDivine ? 'divine-color-grid' : 'color-grid'}>
            {(isDivine ? divineColors : colors).map((color) => (
              isDivine ? (
                <div
                  key={color}
                  className="divine-dot"
                  onClick={() => setSelectedColor(color)}
                >
                  <div className="divine-dot-dark"></div>
                  <div className="divine-dot-accent" style={{ backgroundColor: color }}></div>
                </div>
              ) : (
                <div
                  key={color}
                  className="color-dot"
                  style={{ backgroundColor: color }}
                  onClick={() => setSelectedColor(color)}
                ></div>
              )
            ))}
            {!isDivine && (
              <label className="color-dot color-dot-picker">
                <input
                  type="color"
                  value={selectedColor}
                  onChange={(e) => setSelectedColor(e.target.value)}
                  aria-label="Custom color"
                />
              </label>
            )}
          </div>
        </div>
      )}

    </div>
  )
}

export default LeftPanel
