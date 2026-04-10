import { useState } from 'react'
import './Modal.css'

const BusinessModal = ({ businessInfo, setBusinessInfo, onClose }) => {
  const [form, setForm] = useState({ ...businessInfo })

  const handleSave = () => {
    setBusinessInfo(form)
    onClose()
  }

  return (
    <div className="modal-overlay">
      <div className="modal-box">
        <button className="modal-close" onClick={onClose}>✕</button>
        <h3>Enter your Business details to be printed on your invoice header</h3>

        <div className="modal-field">
          <label>Business Name *</label>
          <input
            type="text"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
          />
        </div>

        <div className="modal-field">
          <label>Phone Number</label>
          <input
            type="text"
            value={form.phone}
            onChange={(e) => setForm({ ...form, phone: e.target.value })}
          />
        </div>

        <div className="modal-field">
          <label>Email ID</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
          />
        </div>

        <div className="modal-field">
          <label>Address</label>
          <textarea
            value={form.address}
            onChange={(e) => setForm({ ...form, address: e.target.value })}
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

export default BusinessModal