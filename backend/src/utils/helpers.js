import { Transformer } from "dgii-ecf";
import { DOMParser } from '@xmldom/xmldom';


export default class Helpers {

    /**
     * Verifica que el XML contenga el nodo <Signature>
     * Lanza un error si no se encuentra
     * @param {string} xml - El XML como string
     */
    async hasXmlSignature(xml) {
        const doc = new DOMParser().parseFromString(xml, 'text/xml');
        const signatureNode = doc.getElementsByTagName('Signature')[0];
        if (!signatureNode) {
            throw new Error('XML Element Signature not found');
        }
    }

}