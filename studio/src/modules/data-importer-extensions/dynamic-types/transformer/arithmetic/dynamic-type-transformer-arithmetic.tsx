import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { ArithmeticTransformerForm } from './arithmetic-transformer-form'

@injectable()
export class DynamicTypeTransformerArithmetic extends DynamicTypeTransformerAbstract {
    readonly id = 'arithmetic'
    readonly label = 'Arithmetic'
    readonly group = 'dataManipulation' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <ArithmeticTransformerForm onChange={ onChange } settings={ settings } />
    }
}
