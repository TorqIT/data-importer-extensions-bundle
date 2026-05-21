import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { ArrayValTransformerForm } from './array-val-transformer-form'

@injectable()
export class DynamicTypeTransformerArrayVal extends DynamicTypeTransformerAbstract {
    readonly id = 'arrayVal'
    readonly label = 'Array Value'
    readonly group = 'dataManipulation' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <ArrayValTransformerForm onChange={ onChange } settings={ settings } />
    }
}
