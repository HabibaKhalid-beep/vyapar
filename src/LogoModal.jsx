import { useRef } from 'react'
import './Modal.css'

const LogoModal = ({ setLogo, onClose }) => {
  const fileRef = useRef(null)

  const handleUpload = (e) => {
    const file = e.target.files[0]
    if (file) {
      const url = URL.createObjectURL(file)
      setLogo(url)
      onClose()
    }
  }

  return (
    <div className="modal-overlay">
      <div className="modal-box">
        <button className="modal-close" onClick={onClose}>✕</button>
        <h3>Upload your Logo image. This Logo will be printed on your invoices.</h3>
        <div className="modal-btns">
          <button className="btn-cancel" onClick={onClose}>CANCEL</button>
          <button className="btn-save" onClick={() => fileRef.current.click()}>UPLOAD</button>
          <input
            type="file"
            accept="image/*"
            ref={fileRef}
            style={{ display: 'none' }}
            onChange={handleUpload}
          />
        </div>
      </div>
    </div>
  )
}

export default LogoModal