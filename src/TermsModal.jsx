import { useState } from 'react'
import './Modal.css'

const TermsModal = ({ terms, setTerms, onClose }) => {
  const [value, setValue] = useState(terms)

  const handleSave = () => {
    setTerms(value)
    onClose()
  }

  return (
    <div className="modal-overlay">
      <div className="modal-box">
        <button className="modal-close" onClick={onClose}>✕</button>
        <h3>Enter Terms and Conditions to be printed on Invoice</h3>
        <div className="modal-field">
          <label>Terms and conditions</label>
          <textarea
            value={value}
            onChange={(e) => setValue(e.target.value)}
          />
        </div>
        <div className="modal-btns">
          <button className="btn-cancel" onClick={onClose}>CANCEL</button>
          <button className="btn-save" onClick={handleSave}>SAVE</button>
        </div>
      </div>
    </div>
  )
}

export default TermsModal